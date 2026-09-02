<?php

namespace App\Services;

use App\Models\AttendanceDay;
use App\Models\AttendanceException;
use App\Models\Holiday;
use App\Models\PermissionRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Traduit les permissions approuvées en jours excusés.
 *
 * Sans ce pont, une permission validée pour un décès ou une convocation
 * d'examen laisse la journée comptée comme une absence sèche dans les
 * statistiques, et donc dans toute note calculée sur l'assiduité.
 */
class PermissionAttendanceSync
{
    /** Nombre de jours au-delà duquel on refuse de traiter une plage (garde-fou saisie). */
    private const MAX_RANGE_DAYS = 366;

    /**
     * Crée les exceptions de présence couvertes par une permission approuvée.
     * Idempotent : rejouer la synchronisation ne crée pas de doublon.
     *
     * @return array{created: int, skipped_present: int, skipped_existing: int, skipped_weekend: int, skipped_holiday: int}
     */
    public function sync(PermissionRequest $request): array
    {
        $report = [
            'created'          => 0,
            'skipped_present'  => 0,
            'skipped_existing' => 0,
            'skipped_weekend'  => 0,
            'skipped_holiday'  => 0,
        ];

        if ($request->status !== 'approved') {
            return $report;
        }

        $range = $this->resolveDateRange($request);
        if (!$range) {
            return $report;
        }

        [$start, $end] = $range;

        $holidays = Holiday::where('is_active', true)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->flip();

        // Une journée de présence est portée par etudiant_id côté stagiaire et
        // par user_id côté employé : on résout l'identifiant une seule fois.
        $etudiantId = $request->etudiant_id ?? $request->user?->etudiant?->id;

        DB::transaction(function () use ($request, $start, $end, $holidays, $etudiantId, &$report) {
            $cursor = $start->copy();

            while ($cursor->lte($end)) {
                $date = $cursor->toDateString();
                $cursor->addDay();

                if (Carbon::parse($date)->isWeekend()) {
                    $report['skipped_weekend']++;
                    continue;
                }

                if ($holidays->has($date)) {
                    $report['skipped_holiday']++;
                    continue;
                }

                // La personne est finalement venue : excuser la journée la
                // retirerait du dénominateur ET du numérateur, faussant son taux
                // de présence dans les deux sens.
                if ($this->hasCheckedIn($request->user_id, $etudiantId, $date)) {
                    $report['skipped_present']++;
                    continue;
                }

                // Une exception déjà posée à la main par un administrateur fait
                // foi : on ne réécrit ni son motif ni son auteur.
                $existing = AttendanceException::where('user_id', $request->user_id)
                    ->whereDate('attendance_date', $date)
                    ->first();

                if ($existing) {
                    $report['skipped_existing']++;
                    continue;
                }

                AttendanceException::create([
                    'user_id'               => $request->user_id,
                    'attendance_date'       => $date,
                    'reason'                => $this->buildReason($request),
                    'created_by'            => $request->decided_by,
                    'permission_request_id' => $request->id,
                ]);

                $report['created']++;
            }
        });

        return $report;
    }

    /**
     * Retire les jours excusés produits par une permission qui n'est plus valide.
     * Ne touche jamais aux exceptions saisies manuellement.
     */
    public function remove(PermissionRequest $request): int
    {
        return AttendanceException::where('permission_request_id', $request->id)->delete();
    }

    /**
     * Détermine la plage de dates couverte, d'après l'effet déclaré du type.
     * Retourne null si le type n'excuse pas de journée entière, ou si les dates
     * attendues sont absentes ou incohérentes.
     *
     * @return array{0: Carbon, 1: Carbon}|null
     */
    public function resolveDateRange(PermissionRequest $request): ?array
    {
        $type = $request->type;

        if (!$type || $type->attendance_effect !== 'excuses_day') {
            return null;
        }

        $fields = $request->fields_data ?? [];
        $fromKey = $type->date_from_field;

        if (!$fromKey || empty($fields[$fromKey])) {
            return null;
        }

        // Un type sans date de fin ne couvre qu'une seule journée.
        $toKey   = $type->date_to_field;
        $rawFrom = $fields[$fromKey];
        $rawTo   = ($toKey && !empty($fields[$toKey])) ? $fields[$toKey] : $rawFrom;

        try {
            $start = Carbon::parse($rawFrom)->startOfDay();
            $end   = Carbon::parse($rawTo)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }

        // Dates saisies à l'envers : on les remet dans l'ordre plutôt que de
        // renvoyer une plage vide qui masquerait silencieusement le problème.
        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        if ($start->diffInDays($end) > self::MAX_RANGE_DAYS) {
            return null;
        }

        return [$start, $end];
    }

    /**
     * Un pointage d'arrivée existe-t-il ce jour-là, côté stagiaire ou employé ?
     */
    private function hasCheckedIn(int $userId, ?int $etudiantId, string $date): bool
    {
        return AttendanceDay::whereDate('attendance_date', $date)
            ->whereNotNull('first_check_in_at')
            ->where(function ($q) use ($userId, $etudiantId) {
                $q->where('user_id', $userId);
                if ($etudiantId) {
                    $q->orWhere('etudiant_id', $etudiantId);
                }
            })
            ->exists();
    }

    private function buildReason(PermissionRequest $request): string
    {
        $label = $request->type->name ?? 'Permission';
        $motif = $request->fields_data['motif'] ?? null;

        return $motif
            ? "{$label} approuvée : {$motif}"
            : "{$label} approuvée";
    }
}
