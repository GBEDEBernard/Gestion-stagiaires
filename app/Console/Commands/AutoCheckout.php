<?php

namespace App\Console\Commands;

use App\Mail\DepartAutomatiqueMail;
use App\Mail\RappelDepartMail;
use App\Models\AppNotification;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AutoCheckout extends Command
{
    protected $signature = 'attendance:auto-checkout {--notify-only : Send notifications without auto check-out}';

    protected $description = 'Auto check-out users at 19:30 and send email reminders at 18:30';

    public function handle(): int
    {
        $notifyOnly = $this->option('notify-only');
        $today = today()->toDateString();

        $usersWithoutCheckout = AttendanceDay::whereDate('attendance_date', $today)
            ->whereNotNull('first_check_in_at')
            ->whereNull('last_check_out_at')
            ->get();

        if ($usersWithoutCheckout->isEmpty()) {
            $this->info('Aucun utilisateur sans pointage de départ.');
            return Command::SUCCESS;
        }

        if ($notifyOnly) {
            $this->sendReminders($usersWithoutCheckout);
            return Command::SUCCESS;
        }

        $this->autoCheckout($usersWithoutCheckout);
        return Command::SUCCESS;
    }

    protected function resolveUserId(AttendanceDay $day): ?int
    {
        if ($day->user_id) {
            return $day->user_id;
        }

        // Stagiaire : résoudre via etudiant -> personnel -> user
        if ($day->etudiant_id) {
            $etudiant = $day->etudiant;
            if ($etudiant) {
                return $etudiant->user?->id;
            }
        }

        // Dernier recours : chercher un user lié au même etudiant_id dans les events
        $event = AttendanceEvent::where('etudiant_id', $day->etudiant_id)
            ->where('event_type', 'check_in')
            ->whereDate('occurred_at', $day->attendance_date)
            ->first();

        return $event?->user_id;
    }

    protected function sendReminders($days): void
    {
        $count = 0;
        $skipped = 0;
        foreach ($days as $day) {
            $userId = $this->resolveUserId($day);
            if (!$userId) {
                $skipped++;
                Log::warning("AutoCheckout: userId introuvable pour AttendanceDay #{$day->id} (user_id={$day->user_id}, etudiant_id={$day->etudiant_id})");
                continue;
            }

            $user = User::find($userId);
            if (!$user) {
                $skipped++;
                Log::warning("AutoCheckout: utilisateur #{$userId} introuvable pour AttendanceDay #{$day->id}");
                continue;
            }

            $existing = AppNotification::where('user_id', $userId)
                ->where('type', 'rappel_depart')
                ->whereDate('created_at', today())
                ->exists();

            if ($existing) continue;

            AppNotification::create([
                'unique_id' => 'rappel_depart_' . (string) Str::uuid(),
                'user_id'   => $userId,
                'type'      => 'rappel_depart',
                'title'     => 'Départ non pointé',
                'message'   => 'Vous avez pointé votre arrivée mais pas encore votre départ. Veuillez pointer votre départ avant 19h30.',
                'icon'      => 'clock',
                'color'     => 'amber',
                'url'       => '/pointage',
            ]);

            try {
                Mail::to($user->email)->send(new RappelDepartMail(
                    $user,
                    $day->first_check_in_at?->format('H:i') ?? '--:--',
                    $day->late_minutes ?? 0,
                ));
            } catch (\Exception $e) {
                Log::error("Échec envoi email rappel départ à {$user->email}: " . $e->getMessage());
            }

            $count++;
        }

        $this->info("{$count} notification(s) de rappel envoyée(s) par email et in-app." . ($skipped ? " ({$skipped} ignoré(s))" : ''));
    }

    protected function autoCheckout($days): void
    {
        $autoTime = today()->setTime(19, 30, 0);
        $count = 0;
        $skipped = 0;

        foreach ($days as $day) {
            $userId = $this->resolveUserId($day);
            if (!$userId) {
                $skipped++;
                Log::warning("AutoCheckout: userId introuvable pour AttendanceDay #{$day->id} (user_id={$day->user_id}, etudiant_id={$day->etudiant_id})");
                continue;
            }

            $user = User::find($userId);
            if (!$user) {
                $skipped++;
                Log::warning("AutoCheckout: utilisateur #{$userId} introuvable pour AttendanceDay #{$day->id}");
                continue;
            }

            try {
                $event = AttendanceEvent::create([
                    'stage_id'         => $day->stage_id,
                    'etudiant_id'      => $day->etudiant_id,
                    'user_id'          => $userId,
                    'event_type'       => 'check_out',
                    'status'           => 'approved',
                    'occurred_at'      => $autoTime,
                    'reason_code'      => 'auto_checkout',
                    'meta'             => [
                        'auto_checkout' => true,
                        'auto_checkout_at' => now()->toDateTimeString(),
                    ],
                ]);

                $workedMinutes = $day->first_check_in_at
                    ? max(0, $day->first_check_in_at->diffInMinutes($autoTime))
                    : 0;

                $day->check_out_event_id = $event->id;
                $day->last_check_out_at  = $autoTime;
                $day->worked_minutes     = $workedMinutes;
                $day->day_status         = $day->day_status === 'present' ? 'completed' : $day->day_status;
                $day->save();

                AppNotification::create([
                    'unique_id' => 'depart_automatique_' . (string) Str::uuid(),
                    'user_id'   => $userId,
                    'type'      => 'depart_automatique',
                    'title'     => 'Départ enregistré automatiquement',
                    'message'   => "Votre départ a été automatiquement enregistré à 19h30 car vous n'avez pas pointé votre départ.",
                    'icon'      => 'logout',
                    'color'     => 'blue',
                    'url'       => '/historique',
                ]);

                try {
                    Mail::to($user->email)->send(new DepartAutomatiqueMail(
                        $user,
                        $day->first_check_in_at?->format('H:i') ?? '--:--',
                        $autoTime->format('H:i'),
                        round($workedMinutes / 60, 1) . 'h',
                    ));
                } catch (\Exception $e) {
                    Log::error("Échec envoi email départ auto à {$user->email}: " . $e->getMessage());
                }

                $count++;
            } catch (\Exception $e) {
                $skipped++;
                Log::error("AutoCheckout: erreur pour AttendanceDay #{$day->id} (user={$userId}): " . $e->getMessage());
            }
        }

        $this->info("{$count} départ(s) automatique(s) enregistré(s) à 19h30." . ($skipped ? " ({$skipped} échec(s))" : ''));
    }
}
