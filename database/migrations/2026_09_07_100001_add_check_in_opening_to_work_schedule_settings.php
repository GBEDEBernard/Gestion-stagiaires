<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * L'heure d'ouverture du pointage d'arrivée était écrite en dur à 07h30 dans
 * PresenceService, et ne s'appliquait qu'aux stagiaires. Un stage commençant à
 * 07h00 voyait donc son arrivée refusée jusqu'à 07h30 : impossible d'être à
 * l'heure. Le réglage rejoint l'horaire de référence, en base comme le reste.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_schedule_settings', function (Blueprint $table) {
            $table->time('check_in_opens_at')->nullable()->after('id');
        });

        DB::table('work_schedule_settings')->update(['check_in_opens_at' => '07:30:00']);
    }

    public function down(): void
    {
        Schema::table('work_schedule_settings', function (Blueprint $table) {
            $table->dropColumn('check_in_opens_at');
        });
    }
};
