<?php

namespace App\Services;

use App\Models\AttendanceDay;
use App\Models\PermissionRequest;
use App\Models\Stage;
use Carbon\Carbon;

/**
 * Construit le rapport de stage d'un stagiaire.
 *
 * Le calcul des jours attendus n'est pas refait ici : il vit déjà dans
 * AdminPresenceService, qui exclut correctement les week-ends, les jours fériés
 * actifs, les jours hors jours de travail du stage, les absences corrigées, les
 * jours futurs et tout ce qui précède la date de début de pointage. On lui
 * délègue le socle, et on ajoute ce qu'il ne calcule pas.
 */
class StageReportService
{
    /** En dessous, un ratio n'est pas une mesure : on affiche la fraction sans pourcentage. */
    public const MIN_RELIABLE_DENOMINATOR = 5;

    public function __construct(
        protected AdminPresenceService $presenceService
    ) {}

    public function build(Stage $stage): array
    {
        $user = $stage->etudiant?->user;

        $from = Carbon::parse($stage->date_debut)->startOfDay();
        $to   = Carbon::parse($stage->date_fin)->startOfDay();

        // Un stage en cours ne se juge que sur ce qui s'est écoulé.
        $today     = today()->startOfDay();
        $isOngoing = $to->gt($today);
        $effectiveTo = $isOngoing ? $today : $to;

        $stats = $user
            ? $this->presenceService->getUserDetailedStats(
                $user->id,
                'custom',
                $from->toDateString(),
                $effectiveTo->toDateString()
            )
            : null;

        $expectedDays = (int) ($stats['total_days'] ?? 0);
        $presentDays  = (int) ($stats['present_days'] ?? 0);

        $days = $this->checkedInDays($stage, $from, $effectiveTo);

        // Deux familles de dénominateurs, chacune cohérente en interne :
        //  - « jours attendus » pour ce qui mesure la venue,
        //  - « jours pointés » pour ce qui mesure la tenue une fois sur place.
        $checkedIn = $days->count();

        $onTime          = $days->where('arrival_status', '!=', 'late')->count();
        $completeDays    = $days->filter(fn($d) => $d->first_check_in_at && $d->last_check_out_at)->count();
        $noEarlyDeparture = $days->filter(fn($d) => (int) ($d->early_departure_minutes ?? 0) === 0)->count();

        $submittedReports = $stage->dailyReports()
            ->whereNotNull('submitted_at')
            ->whereBetween('report_date', [$from->toDateString(), $effectiveTo->toDateString()])
            ->count();

        $anomalies = $this->anomalies($stage, $from, $effectiveTo);
        $permissions = $this->permissions($user?->id, $from, $effectiveTo);

        $workedMinutes   = (int) $days->sum('worked_minutes');
        $expectedMinutes = $this->expectedMinutes($stage, $expectedDays);

        return [
            'stage'     => $stage,
            'user'      => $user,
            'window'    => [
                'from'         => $from,
                'to'           => $to,
                'effective_to' => $effectiveTo,
                'is_ongoing'   => $isOngoing,
            ],
            'counts' => [
                'expected_days'   => $expectedDays,
                'present_days'    => $presentDays,
                'absent_days'     => max(0, $expectedDays - $presentDays),
                'checked_in_days' => $checkedIn,
                'late_days'       => (int) ($stats['late_days'] ?? 0),
                'late_minutes'    => (int) ($stats['total_late_minutes'] ?? 0),
                'worked_hours'    => round($workedMinutes / 60, 1),
                'avg_daily_hours' => (float) ($stats['avg_daily_hours'] ?? 0),
            ],
            'ratios' => [
                'assiduite'          => $this->ratio($presentDays, $expectedDays),
                'absenteisme'        => $this->ratio(max(0, $expectedDays - $presentDays), $expectedDays),
                'ponctualite'        => $this->ratio($onTime, $checkedIn),
                'journees_completes' => $this->ratio($completeDays, $checkedIn),
                'tenue_poste'        => $this->ratio($noEarlyDeparture, $checkedIn),
                'comptes_rendus'     => $this->ratio($submittedReports, $checkedIn),
                'volume_horaire'     => $expectedMinutes
                    ? $this->ratio($workedMinutes, $expectedMinutes)
                    : null,
                'incidents'          => $this->ratio($anomalies['open'], $checkedIn),
                'permissions'        => $this->ratio($permissions['days_covered'], $expectedDays),
            ],
            'anomalies'   => $anomalies,
            'permissions' => $permissions,
            'chart'       => $stats['chart_data'] ?? null,
        ];
    }

    /**
     * Données destinées au document remis.
     *
     * Une fois l'évaluation finalisée, les chiffres proviennent de l'instantané
     * figé et non d'un recalcul : c'est tout l'intérêt du gel. Sans cela, une
     * absence corrigée après coup ferait diverger le PDF réimprimé de celui déjà
     * remis à l'école, sur un document portant la même note.
     */
    public function forDocument(Stage $stage): array
    {
        $live       = $this->build($stage);
        $evaluation = $stage->evaluation;
        $snapshot   = $evaluation?->isFinalized() ? $evaluation->attendance_snapshot : null;

        if (!$snapshot) {
            return $live + ['is_frozen' => false, 'frozen_at' => null];
        }

        return array_merge($live, [
            'counts'    => $snapshot['counts'] ?? $live['counts'],
            'ratios'    => $snapshot['ratios'] ?? $live['ratios'],
            'is_frozen' => true,
            'frozen_at' => $snapshot['frozen_at'] ?? null,
        ]);
    }

    /**
     * Une fraction reste une fraction : le pourcentage n'est renseigné que
     * lorsque le dénominateur porte assez de jours pour vouloir dire quelque
     * chose. 100 % sur deux jours n'est pas une performance.
     *
     * @return array{numerator: int, denominator: int, rate: float|null, reliable: bool}
     */
    public function ratio(int $numerator, int $denominator): array
    {
        $reliable = $denominator >= self::MIN_RELIABLE_DENOMINATOR;

        return [
            'numerator'   => $numerator,
            'denominator' => $denominator,
            'rate'        => ($denominator > 0 && $reliable) ? round($numerator / $denominator, 4) : null,
            'reliable'    => $reliable,
        ];
    }

    /** Journées du stage où un pointage d'arrivée a bien eu lieu. */
    private function checkedInDays(Stage $stage, Carbon $from, Carbon $to)
    {
        return $stage->attendanceDays()
            ->whereNotNull('first_check_in_at')
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('attendance_date')
            ->get();
    }

    private function anomalies(Stage $stage, Carbon $from, Carbon $to): array
    {
        $items = $stage->attendanceAnomalies()
            ->whereBetween('detected_at', [$from->toDateString() . ' 00:00:00', $to->toDateString() . ' 23:59:59'])
            ->orderByDesc('detected_at')
            ->get();

        return [
            'total'       => $items->count(),
            'open'        => $items->whereIn('status', ['open', 'flagged'])->count(),
            'resolved'    => $items->where('status', 'resolved')->count(),
            'by_type'     => $items->groupBy('anomaly_type')->map->count()->sortDesc(),
            'by_severity' => $items->groupBy('severity')->map->count(),
            'items'       => $items,
        ];
    }

    /**
     * Permissions approuvées sur la période, et nombre de jours qu'elles ont
     * effectivement retirés du décompte des absences.
     */
    private function permissions(?int $userId, Carbon $from, Carbon $to): array
    {
        if (!$userId) {
            return ['approved' => 0, 'days_covered' => 0, 'items' => collect()];
        }

        $items = PermissionRequest::with('type')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->whereBetween('created_at', [$from->copy()->subMonths(2), $to->copy()->endOfDay()])
            ->get();

        $daysCovered = \App\Models\AttendanceException::where('user_id', $userId)
            ->whereNotNull('permission_request_id')
            ->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()])
            ->count();

        return [
            'approved'     => $items->count(),
            'days_covered' => $daysCovered,
            'items'        => $items,
        ];
    }

    /**
     * Volume horaire attendu, d'après l'horaire déclaré sur le stage.
     * Retourne 0 si l'horaire n'est pas renseigné : mieux vaut masquer
     * l'indicateur que d'inventer une base de comparaison.
     */
    private function expectedMinutes(Stage $stage, int $expectedDays): int
    {
        if (!$stage->expected_check_in_time || !$stage->expected_check_out_time) {
            return 0;
        }

        $in  = Carbon::parse($stage->expected_check_in_time);
        $out = Carbon::parse($stage->expected_check_out_time);

        $perDay = $in->diffInMinutes($out, false);

        return $perDay > 0 ? $perDay * $expectedDays : 0;
    }
}
