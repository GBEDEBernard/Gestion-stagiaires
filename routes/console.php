<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Présences : rappels et clôture des départs non pointés ──────────────
// Autrefois déclarés dans l'ancien app/Console/Kernel.php, que Laravel 12
// ignore. Ils doivent vivre ici pour être réellement planifiés.

// Rappel du soir : « vous n'avez pas encore pointé votre départ ».
Schedule::command('attendance:auto-checkout --notify-only')
    ->dailyAt('18:30')
    ->withoutOverlapping();

// Clôture de la VEILLE, à 05h00. On ne déclare pas un départ oublié le soir
// même : la personne peut rester travailler et pointer à 22h. Le lendemain,
// la question est tranchée, et l'écran de pointage demandera l'heure réelle.
Schedule::command('attendance:auto-checkout')
    ->dailyAt('05:00')
    ->withoutOverlapping();

// Rattrape les permissions approuvées vers les jours excusés (idempotent).
// Couvre les cas non passés par PermissionRequestService::decide() (imports,
// corrections en base, appels API directs).
Schedule::command('permissions:sync-attendance')->dailyAt('06:00');
