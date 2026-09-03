<?php

namespace App\Services;

use App\Models\AttendanceCorrection;
use App\Models\AttendanceDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Rétablit l'heure réelle d'une journée mal enregistrée — l'arrivée qui n'a pas
 * pu être scannée, le départ oublié et clôturé d'office.
 *
 * La journée est modifiée pour que tous les calculs existants en tiennent
 * compte sans être touchés, et les valeurs d'origine sont conservées dans la
 * correction : le fait reste consultable, seul son effet sur la ponctualité
 * ou le volume horaire disparaît.
 */
class AttendanceCorrectionService
{
    public function apply(User $owner, AttendanceDay $day, string $time, string $reason, User $admin): AttendanceCorrection
    {
        if (!$day->first_check_in_at) {
            throw ValidationException::withMessages([
                'time' => "Cette journée n'a aucun pointage d'arrivée. Utilisez la correction d'absence.",
            ]);
        }

        if ($day->correction()->exists()) {
            throw ValidationException::withMessages([
                'time' => "Cette journée a déjà été corrigée. Annulez la correction en place avant d'en poser une autre.",
            ]);
        }

        $corrected = $this->buildCorrectedMoment($day, $time);

        if ($corrected->gt($day->first_check_in_at)) {
            throw ValidationException::withMessages([
                'time' => "L'heure rétablie doit être antérieure à l'heure enregistrée : une correction sert à reconnaître une arrivée plus tôt, pas plus tard.",
            ]);
        }

        return DB::transaction(function () use ($owner, $day, $corrected, $reason, $admin) {
            $correction = AttendanceCorrection::create([
                'attendance_day_id'       => $day->id,
                'user_id'                 => $owner->id,
                'original_check_in_at'    => $day->first_check_in_at,
                'original_arrival_status' => $day->arrival_status,
                'original_late_minutes'   => (int) ($day->late_minutes ?? 0),
                'corrected_check_in_at'   => $corrected,
                'reason'                  => $reason,
                'created_by'              => $admin->id,
            ]);

            $day->update([
                'first_check_in_at' => $corrected,
                // Valeur écrite par PresenceService::computeArrivalStatus().
                'arrival_status'    => 'ontime',
                'late_minutes'      => 0,
            ]);

            return $correction;
        });
    }

    /**
     * Rétablit l'heure de départ d'une journée clôturée d'office.
     *
     * La personne a déclaré une heure ; celle-ci ne s'applique pas sur sa
     * seule parole. C'est l'administrateur qui tranche et pose la valeur,
     * après quoi la journée cesse d'être une journée oubliée.
     */
    public function applyCheckOut(User $owner, AttendanceDay $day, string $time, string $reason, User $admin): AttendanceCorrection
    {
        if (!$day->last_check_out_at) {
            throw ValidationException::withMessages([
                'time' => "Cette journée n'a aucune heure de départ à corriger.",
            ]);
        }

        if ($day->correctionDepart()->exists()) {
            throw ValidationException::withMessages([
                'time' => "Le départ de cette journée a déjà été corrigé. Annulez la correction en place avant d'en poser une autre.",
            ]);
        }

        $corrected = $this->buildCorrectedMoment($day, $time);

        if ($day->first_check_in_at && $corrected->lessThanOrEqualTo($day->first_check_in_at)) {
            throw ValidationException::withMessages([
                'time' => "L'heure de départ doit être postérieure à l'heure d'arrivée.",
            ]);
        }

        return DB::transaction(function () use ($owner, $day, $corrected, $reason, $admin) {
            $correction = AttendanceCorrection::create([
                'attendance_day_id'      => $day->id,
                'user_id'                => $owner->id,
                'field'                  => 'check_out',
                'original_check_out_at'  => $day->last_check_out_at,
                'corrected_check_out_at' => $corrected,
                'reason'                 => $reason,
                'created_by'             => $admin->id,
            ]);

            $day->update([
                'last_check_out_at' => $corrected,
                'worked_minutes'    => $day->first_check_in_at
                    ? app(WorkScheduleResolver::class)->workedMinutes($day->stage, $day->first_check_in_at, $corrected)
                    : 0,
                'departure_status'  => 'corrected',
            ]);

            return $correction;
        });
    }

    /**
     * Ce que la personne déclare elle-même, le lendemain, sur l'écran de
     * pointage. Rien n'est appliqué : on consigne, et on renvoie vers le
     * responsable.
     */
    public function claimCheckOut(AttendanceDay $day, string $time, string $reason): AttendanceDay
    {
        $claimed = $this->buildCorrectedMoment($day, $time);

        if ($day->first_check_in_at && $claimed->lessThanOrEqualTo($day->first_check_in_at)) {
            throw ValidationException::withMessages([
                'claimed_time' => "L'heure indiquée doit être postérieure à votre arrivée de ce jour-là ({$day->first_check_in_at->format('H:i')}).",
            ]);
        }

        $day->update([
            'claimed_check_out_at'     => $claimed,
            'claimed_check_out_reason' => $reason,
            'claimed_at'               => now(),
            'departure_status'         => 'claimed',
        ]);

        return $day->refresh();
    }

    /**
     * Annule une correction et restitue exactement les valeurs constatées.
     */
    public function revert(AttendanceCorrection $correction): void
    {
        DB::transaction(function () use ($correction) {
            $day = $correction->attendanceDay;

            if ($day && $correction->field === 'check_out') {
                $day->update([
                    'last_check_out_at' => $correction->original_check_out_at,
                    'worked_minutes'    => $day->first_check_in_at && $correction->original_check_out_at
                        ? app(WorkScheduleResolver::class)->workedMinutes($day->stage, $day->first_check_in_at, $correction->original_check_out_at)
                        : 0,
                    // La journée redevient ce qu'elle était : clôturée d'office,
                    // avec la déclaration de l'intéressé toujours en attente.
                    'departure_status'  => $day->claimed_at ? 'claimed' : 'auto_closed',
                ]);
            } elseif ($day) {
                $day->update([
                    'first_check_in_at' => $correction->original_check_in_at,
                    'arrival_status'    => $correction->original_arrival_status,
                    'late_minutes'      => $correction->original_late_minutes,
                ]);
            }

            $correction->delete();
        });
    }

    /**
     * Compose l'horodatage corrigé : l'heure saisie, sur la date de la journée.
     */
    private function buildCorrectedMoment(AttendanceDay $day, string $time): Carbon
    {
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $time, $m)) {
            throw ValidationException::withMessages([
                'time' => "Indiquez une heure valide au format HH:MM.",
            ]);
        }

        return Carbon::parse($day->attendance_date)
            ->startOfDay()
            ->setTime((int) $m[1], (int) $m[2]);
    }
}
