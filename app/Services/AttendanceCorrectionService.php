<?php

namespace App\Services;

use App\Models\AttendanceCorrection;
use App\Models\AttendanceDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Rétablit l'heure d'arrivée réelle d'une journée mal enregistrée.
 *
 * La journée est modifiée pour que tous les calculs existants en tiennent
 * compte sans être touchés, et les valeurs d'origine sont conservées dans la
 * correction : le fait reste consultable, seul son effet sur la ponctualité
 * disparaît.
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
     * Annule une correction et restitue exactement les valeurs constatées.
     */
    public function revert(AttendanceCorrection $correction): void
    {
        DB::transaction(function () use ($correction) {
            $day = $correction->attendanceDay;

            if ($day) {
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
