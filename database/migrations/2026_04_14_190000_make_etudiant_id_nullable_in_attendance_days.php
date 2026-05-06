<?php

// Migration pour corriger erreur SQL "etudiant_id n'a pas de valeur par défaut"
// Permet aux employés (user_id) de créer AttendanceDay sans etudiant_id/stage_id
// Compatible avec logique existante: etudiants = etudiant_id+stage_id, employés = user_id

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_days', function (Blueprint $table) {
            // 1. Supprimer contraintes bloquantes
            $table->dropForeign(['etudiant_id']);
            $table->dropIndex(['etudiant_id', 'attendance_date']); // index existant

            // 2. Rendre etudiant_id nullable
            $table->foreignId('etudiant_id')->nullable()->change();

            // 3. Recréer FK nullable (nullOnDelete pour employés)
            $table->foreign('etudiant_id')
                ->references('id')->on('etudiants')
                ->nullOnDelete();

            // 4. Index optimisé (partiel, évite doublons etudiant_id+date)
            $table->index(['etudiant_id', 'attendance_date'], 'attendance_days_etudiant_date_index');

            // Index pour employés (user_id + date)
            $table->index(['user_id', 'attendance_date'], 'attendance_days_user_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_days', function (Blueprint $table) {
            // Restaurer contrainte originale
            $table->dropForeign(['etudiant_id']);
            $table->dropIndex(['attendance_days_etudiant_date_index']);
            $table->dropIndex(['attendance_days_user_date_index']);

            $table->foreignId('etudiant_id')->nullable(false)->change();
            $table->foreign('etudiant_id')
                ->references('id')->on('etudiants')
                ->cascadeOnDelete();

            $table->index(['etudiant_id', 'attendance_date']);
        });
    }
};
