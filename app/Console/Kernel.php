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

        // Résumé IA hebdomadaire chaque vendredi à 20h00
        $schedule->command('summaries:generate weekly')
            ->weekly()
            ->fridays()
            ->at('20:00')
            ->withoutOverlapping();

        // Résumé IA mensuel le 1er de chaque mois à 20h00
        $schedule->command('summaries:generate monthly')
            ->monthlyOn(1, '20:00')
            ->withoutOverlapping();

        // Résumé IA annuel le 1er janvier à 20h00
        $schedule->command('summaries:generate yearly')
            ->yearlyOn(1, 1, '20:00')
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
