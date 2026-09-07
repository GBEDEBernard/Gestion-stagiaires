<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tâches planifiées
|--------------------------------------------------------------------------
|
| Elles vivaient dans app/Console/Kernel.php, hérité de Laravel 10. Depuis
| Laravel 11, ce fichier n'est plus chargé : le planificateur ne connaissait
| aucune tâche, et `schedule:list` le disait sans que personne ne le lise.
| Rien ne tournait — ni le rappel du soir, ni la clôture des journées, ni les
| résumés, ni la désactivation des stages échus.
|
| Rappel : tout ceci suppose une entrée cron sur le serveur, une seule pour
| tout le monde :
|     * * * * * cd /chemin/du/projet && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Rappel du soir : « vous n'avez pas encore pointé votre départ ».
Schedule::command('attendance:auto-checkout --notify-only')
    ->dailyAt('18:30')
    ->withoutOverlapping();

// Clôture des journées passées, à 05h00. On ne déclare pas un départ oublié
// le soir même : la personne peut rester travailler et pointer à 22h. Le
// lendemain, la question est tranchée, et l'écran de pointage demandera
// l'heure réelle avant que quiconque ait besoin d'y penser.
Schedule::command('attendance:auto-checkout')
    ->dailyAt('05:00')
    ->withoutOverlapping();

// Puis, la clôture faite : avertir ceux qui n'ont jamais déclaré, et suspendre
// ceux qui n'ont pas répondu à l'avertissement de la veille.
Schedule::command('attendance:suspend-unresolved')
    ->dailyAt('05:15')
    ->withoutOverlapping();

// Résumé IA hebdomadaire, chaque vendredi à 20h00.
Schedule::command('summaries:generate weekly')
    ->weeklyOn(5, '20:00')
    ->withoutOverlapping();

// Résumé IA mensuel, le 1er de chaque mois à 20h00.
Schedule::command('summaries:generate monthly')
    ->monthlyOn(1, '20:00')
    ->withoutOverlapping();

// Résumé IA annuel, le 1er janvier à 20h00.
Schedule::command('summaries:generate yearly')
    ->yearlyOn(1, 1, '20:00')
    ->withoutOverlapping();

// Désactivation des comptes stagiaires dont le stage est terminé.
Schedule::command('students:deactivate-expired')
    ->dailyAt('00:05')
    ->withoutOverlapping();
