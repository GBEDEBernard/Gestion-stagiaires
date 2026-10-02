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

        // Clôture TON JOUR de toutes les journées pointées sans départ.
        // On le fait à 00h00 (début de journée) pour que demain matin,
        // dès l'arrivée pointée, le modal de départ oublié soit déjà
        // utilisable par tous (employés, stagiaires, admin+employés).
        $schedule->command('attendance:auto-checkout --date=yesterday')
            ->dailyAt('00:02')
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
