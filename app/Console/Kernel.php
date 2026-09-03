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
        // Rappel du soir : « vous n'avez pas encore pointé votre départ ».
        $schedule->command('attendance:auto-checkout --notify-only')
            ->dailyAt('18:30')
            ->withoutOverlapping();

        // Clôture de la VEILLE, à 05h00. On ne déclare pas un départ oublié le
        // soir même : la personne peut rester travailler et pointer à 22h.
        // Le lendemain, la question est tranchée, et l'écran de pointage
        // demandera l'heure réelle avant que quiconque ait besoin d'y penser.
        $schedule->command('attendance:auto-checkout')
            ->dailyAt('05:00')
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

        // Désactivation automatique des comptes stagiaires dont le stage est terminé
        $schedule->command('students:deactivate-expired')
            ->dailyAt('00:05')
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
