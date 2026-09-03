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
    protected $signature = 'attendance:auto-checkout
        {--notify-only : Rappeler les départs non pointés du jour, sans rien clôturer}
        {--date= : Journée à clôturer (par défaut, la veille)}';

    protected $description = "Rappelle les départs non pointés le soir, et clôture la veille à l'heure de fin prévue";

    public function handle(): int
    {
        try {
            $notifyOnly = $this->option('notify-only');

            // Le rappel porte sur aujourd'hui ; la clôture, sur la veille.
            // On ne peut pas déclarer un départ oublié le soir même : la
            // personne est peut-être encore là, et pointera à 22h.
            $date = $notifyOnly
                ? today()
                : ($this->option('date') ? Carbon::parse($this->option('date'))->startOfDay() : today()->subDay());

            $usersWithoutCheckout = AttendanceDay::whereDate('attendance_date', $date->toDateString())
                ->whereNotNull('first_check_in_at')
                ->whereNull('last_check_out_at')
                ->get();

            if ($usersWithoutCheckout->isEmpty()) {
                $this->info("Aucun départ non pointé le {$date->format('d/m/Y')}.");
                return Command::SUCCESS;
            }

            if ($notifyOnly) {
                $this->sendReminders($usersWithoutCheckout);
            } else {
                $this->autoCheckout($usersWithoutCheckout, $date);
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('AutoCheckout: échec général — ' . $e->getMessage(), [
                'notify-only' => $this->option('notify-only'),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error('Erreur : ' . $e->getMessage());
            return Command::FAILURE;
        }
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
                'message'   => "Vous avez pointé votre arrivée mais pas encore votre départ. Pointez-le avant de quitter le site : demain, la journée sera clôturée à l'heure de fin prévue.",
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

    protected function autoCheckout($days, Carbon $date): void
    {
        $resolver = app(\App\Services\WorkScheduleResolver::class);
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

            // L'heure de fin prévue de CETTE journée-là, jamais une heure fixe :
            // clôturer à 19h30 un stage qui finit à 12h créditerait sept heures
            // et demie de travail imaginaire, et oublier de pointer deviendrait
            // plus avantageux que pointer.
            $autoTime = $resolver->expectedDeparture($day->stage, $date);

            // Arrivée après l'heure de fin (journée décalée, rattrapage) : on
            // ne peut pas fermer avant d'avoir ouvert.
            if ($day->first_check_in_at && $autoTime->lessThanOrEqualTo($day->first_check_in_at)) {
                $autoTime = $day->first_check_in_at->copy();
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

                // Même calcul que sur un départ pointé : la pause est déduite.
                // Deux formules pour la même journée finissaient par donner
                // deux volumes horaires différents.
                $workedMinutes = $day->first_check_in_at
                    ? $resolver->workedMinutes($day->stage, $day->first_check_in_at, $autoTime)
                    : 0;

                $day->check_out_event_id = $event->id;
                $day->last_check_out_at  = $autoTime;
                $day->worked_minutes     = $workedMinutes;
                $day->day_status         = $day->day_status === 'present' ? 'completed' : $day->day_status;
                // La journée porte sa marque : elle compte dans les heures,
                // mais elle n'est pas une journée pointée pour autant.
                $day->departure_status   = 'auto_closed';
                $day->save();

                AppNotification::create([
                    'unique_id' => 'depart_automatique_' . (string) Str::uuid(),
                    'user_id'   => $userId,
                    'type'      => 'depart_automatique',
                    'title'     => 'Départ enregistré automatiquement',
                    'message'   => "Vous n'avez pas pointé votre départ du {$date->format('d/m/Y')}. La journée a été clôturée d'office à {$autoTime->format('H:i')}. Indiquez votre heure réelle lors de votre prochain pointage, puis voyez votre responsable.",
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

        $this->info("{$count} journée(s) du {$date->format('d/m/Y')} clôturée(s) à l'heure de fin prévue." . ($skipped ? " ({$skipped} échec(s))" : ''));
    }
}
