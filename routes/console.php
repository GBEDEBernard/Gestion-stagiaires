<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rattrape les permissions approuvées vers les jours excusés (idempotent).
// Couvre les cas non passés par PermissionRequestService::decide() (imports,
// corrections en base, appels API directs).
Schedule::command('permissions:sync-attendance')->dailyAt('06:00');
