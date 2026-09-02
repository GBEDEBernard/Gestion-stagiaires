<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Horaire par jour de la semaine, et pause déductible.
 *
 * L'horaire du stage devient un défaut ; chaque jour peut le redéfinir, ce qui
 * rend enfin exprimable la demi-journée du mercredi. Sans cela, un stage n'a
 * qu'un seul horaire pour tous ses jours.
 *
 * break_minutes permet de distinguer le temps de présence du temps de travail :
 * jusqu'ici, une pause de deux heures était comptée comme du travail.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage_jour', function (Blueprint $table) {
            // NULL = ce jour suit l'horaire du stage.
            $table->time('start_time')->nullable()->after('jour_id');
            $table->time('end_time')->nullable()->after('start_time');
            $table->unsignedSmallInteger('break_minutes')->nullable()->after('end_time');
        });

        Schema::table('stages', function (Blueprint $table) {
            // Pause par défaut du stage, déduite du temps de présence.
            $table->unsignedSmallInteger('break_minutes')->default(0)->after('allowed_early_departure_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('stage_jour', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'break_minutes']);
        });

        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn('break_minutes');
        });
    }
};
