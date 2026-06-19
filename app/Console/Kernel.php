<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Rappel départ à 18h30 (email + in-app)
        $schedule->command('attendance:auto-checkout --notify-only')
            ->dailyAt('18:30')
            ->withoutOverlapping();

        // Auto check-out à 19h30 (email + in-app)
        $schedule->command('attendance:auto-checkout')
            ->dailyAt('19:30')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
