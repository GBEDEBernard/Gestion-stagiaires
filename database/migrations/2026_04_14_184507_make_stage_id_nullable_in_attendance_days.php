<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_days', function (Blueprint $table) {
            // 1. Supprimer la FK d'abord
            $table->dropForeign(['stage_id']);

            // 2. Supprimer l'index unique qui bloque (contient stage_id)
            $table->dropUnique('attendance_days_stage_id_attendance_date_unique');

            // 3. Rendre stage_id nullable
            $table->foreignId('stage_id')->nullable()->change();

            // 4. Recréer la FK nullable
            $table->foreign('stage_id')
                ->references('id')->on('stages')
                ->nullOnDelete();

            // 5. Recréer un index unique partiel compatible NULL
            //    (attendance_date + etudiant_id pour stagiaires,
            //     attendance_date + user_id pour employés — géré applicativement)
            $table->index(['stage_id', 'attendance_date'], 'attendance_days_stage_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_days', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->dropIndex('attendance_days_stage_date_index');

            $table->foreignId('stage_id')->nullable(false)->change();
            $table->foreign('stage_id')
                ->references('id')->on('stages')
                ->cascadeOnDelete();

            $table->unique(['stage_id', 'attendance_date'], 'attendance_days_stage_id_attendance_date_unique');
        });
    }
};