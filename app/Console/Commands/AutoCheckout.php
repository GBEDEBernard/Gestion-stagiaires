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
        {--date= : Ne clôturer que cette journée-là}
        {--days=7 : Profondeur de rattrapage, en jours, si le cron a sauté un tour}';

    protected $description = "Rappelle les départs non pointés le soir, et clôture les journées passées à l'heure de fin prévue";

    public function handle(): int
    {
        try {
            $notifyOnly = $this->option('notify-only');

            // Le rappel porte sur aujourd'hui ; la clôture, sur les journées
            // déjà passées. On ne peut pas déclarer un départ oublié le soir
            // même : la personne est peut-être encore là, et pointera à 22h.
            if ($notifyOnly) {
                $days = $this->openDays(today(), today());

                if ($days->isEmpty()) {
                    $this->info("Aucun départ non pointé aujourd'hui.");
                    return Command::SUCCESS;
                }

                $this->sendReminders($days);
                return Command::SUCCESS;
            }

            // Une nuit sans cron laissait la journée ouverte pour toujours :
            // jamais clôturée, jamais réclamée. On remonte donc sur quelques
            // jours, sans aller chercher un arriéré historique qui noierait
            // tout le monde sous les notifications au premier déploiement.
            if ($this->option('date')) {
                $depuis = $jusqua = Carbon::parse($this->option('date'))->startOfDay();
            } else {
                $jusqua = today()->subDay();
                $depuis = today()->subDays(max(1, (int) $this->option('days')));
            }

            $days = $this->openDays($depuis, $jusqua);

            if ($days->isEmpty()) {
                $this->info("Aucun départ non pointé entre le {$depuis->format('d/m/Y')} et le {$jusqua->format('d/m/Y')}.");
                return Command::SUCCESS;
            }

            $this->autoCheckout($days);

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

    /** Les journées ouvertes d'une période : arrivée pointée, départ jamais. */
    protected function openDays(Carbon $depuis, Carbon $jusqua)
    {
        return AttendanceDay::whereBetween('attendance_date', [$depuis->toDateString(), $jusqua->toDateString()])
            ->whereNotNull('first_check_in_at')
            ->whereNull('last_check_out_at')
            // Sans ce chargement, l'horaire de chaque journée coûtait une
            // requête de plus.
            ->with('stage')
            ->orderBy('attendance_date')
            ->get();
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

    protected function autoCheckout($days): void
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
            $date     = $day->attendance_date->copy()->startOfDay();
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

        $this->info("{$count} journée(s) clôturée(s) à l'heure de fin prévue." . ($skipped ? " ({$skipped} échec(s))" : ''));
    }
}
