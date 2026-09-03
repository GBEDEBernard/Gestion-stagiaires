<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La table ne corrigeait qu'une heure d'arrivée. Un départ oublié demande la
 * même chose au bout de la chaîne : l'administrateur pose l'heure réelle, et
 * la valeur d'office reste consignée.
 *
 * Une journée peut porter les deux corrections — arrivée mal enregistrée le
 * matin, départ oublié le soir — d'où l'unicité déplacée sur le couple
 * (journée, champ corrigé).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->string('field', 16)->default('check_in')->after('user_id');
            $table->dateTime('original_check_out_at')->nullable()->after('original_late_minutes');
            $table->dateTime('corrected_check_out_at')->nullable()->after('corrected_check_in_at');
        });

        // La colonne d'arrivée devient facultative : une correction de départ
        // n'en porte aucune.
        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->dateTime('corrected_check_in_at')->nullable()->change();
        });

        // L'unique composite est posé d'abord : MySQL refuse de supprimer
        // l'index simple tant qu'aucun autre ne peut porter la clé étrangère.
        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->unique(['attendance_day_id', 'field']);
        });

        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->dropUnique(['attendance_day_id']);
        });
    }

    public function down(): void
    {
        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->unique(['attendance_day_id']);
            $table->dropUnique(['attendance_day_id', 'field']);
            $table->dropColumn(['field', 'original_check_out_at', 'corrected_check_out_at']);
        });
    }
};
