<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\AttendanceDay;
use App\Models\AttendanceEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCheckout extends Command
{
    protected $signature = 'attendance:auto-checkout {--notify-only : Send notifications without auto check-out}';

    protected $description = 'Auto check-out users at 19:00 and send reminders at 18:30';

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

    protected function sendReminders($days): void
    {
        $count = 0;
        foreach ($days as $day) {
            $userId = $day->user_id ?? $day->etudiant?->user?->id;
            if (!$userId) continue;

            $existing = AppNotification::where('user_id', $userId)
                ->where('type', 'rappel_depart')
                ->whereDate('created_at', today())
                ->exists();

            if ($existing) continue;

            AppNotification::create([
                'user_id'  => $userId,
                'type'     => 'rappel_depart',
                'title'    => 'Départ non pointé',
                'message'  => 'Vous avez pointé votre arrivée mais pas encore votre départ. Veuillez pointer votre départ avant 19h00.',
                'icon'     => 'clock',
                'color'    => 'amber',
                'url'      => '/pointage',
            ]);
            $count++;
        }

        $this->info("{$count} notification(s) de rappel envoyée(s).");
    }

    protected function autoCheckout($days): void
    {
        $count = 0;
        foreach ($days as $day) {
            $userId = $day->user_id ?? $day->etudiant?->user?->id;
            if (!$userId) continue;

            $user = User::find($userId);
            if (!$user) continue;

            $autoTime = today()->setTime(19, 0, 0);

            $event = AttendanceEvent::create([
                'stage_id'         => $day->stage_id,
                'etudiant_id'      => $day->etudiant_id,
                'user_id'          => $day->user_id,
                'event_type'       => 'check_out',
                'status'           => 'approved',
                'occurred_at'      => $autoTime,
                'reason_code'      => 'auto_checkout',
                'meta'             => [
                    'auto_checkout' => true,
                    'auto_checkout_at' => now()->toDateTimeString(),
                ],
            ]);

            $day->check_out_event_id = $event->id;
            $day->last_check_out_at  = $autoTime;
            $day->worked_minutes     = $day->first_check_in_at
                ? max(0, $day->first_check_in_at->diffInMinutes($autoTime))
                : 0;
            $day->day_status         = $day->day_status === 'present' ? 'completed' : $day->day_status;
            $day->save();

            AppNotification::create([
                'user_id'  => $userId,
                'type'     => 'depart_automatique',
                'title'    => 'Départ enregistré automatiquement',
                'message'  => "Votre départ a été automatiquement enregistré à 19h00 car vous n'avez pas pointé votre départ.",
                'icon'     => 'logout',
                'color'    => 'blue',
                'url'      => '/historique',
            ]);

            $count++;
        }

        $this->info("{$count} départ(s) automatique(s) enregistré(s).");
    }
}
