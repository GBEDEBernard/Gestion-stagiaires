<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-003 / Phase 1 — Les tâches deviennent « possédables » par un producteur
     * (employé OU étudiant). On ajoute owner_id et on rend etudiant_id / stage_id
     * nullable (un employé n'a pas de stage).
     */
    public function up(): void
    {
        // 1) Ajout de owner_id (le producteur propriétaire de la tâche)
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('owner_id')
                ->nullable()
                ->after('assigned_by')
                ->constrained('users')
                ->nullOnDelete();
        });

        // 2) Suppression des FK existantes avant de modifier les colonnes
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['etudiant_id']);
            $table->dropForeign(['stage_id']);
        });

        // 3) Passage des colonnes en nullable
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('etudiant_id')->nullable()->change();
            $table->foreignId('stage_id')->nullable()->change();
        });

        // 4) Recréation des FK en nullOnDelete (cohérent avec le caractère nullable)
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->nullOnDelete();
            $table->foreign('stage_id')->references('id')->on('stages')->nullOnDelete();
        });

        // 5) Backfill : à défaut de pouvoir résoudre simplement le compte du
        //    producteur, on initialise owner_id avec le créateur (assigned_by).
        DB::statement('UPDATE tasks SET owner_id = assigned_by WHERE owner_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['etudiant_id']);
            $table->dropForeign(['stage_id']);
        });

        // Restauration : colonnes non nullables + FK cascade d'origine.
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('etudiant_id')->nullable(false)->change();
            $table->foreignId('stage_id')->nullable(false)->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->cascadeOnDelete();
            $table->foreign('stage_id')->references('id')->on('stages')->cascadeOnDelete();
        });
    }
};
