<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Rend stage_id, etudiant_id et user_id nullable dans attendance_anomalies
     * Pour supporter les pointages employés (sans stage/étudiant).
     */
    public function up(): void
    {
        Schema::table('attendance_anomalies', function (Blueprint $table) {
            // Supprimer contraintes FK existantes (NOT NULL)
            $table->dropForeign(['stage_id']);
            $table->dropForeign(['etudiant_id']);

            // Recreéer comme nullable
            $table->foreignId('stage_id')->nullable()->change();
            $table->foreign('stage_id')->references('id')->on('stages')->nullOnDelete();

            $table->foreignId('etudiant_id')->nullable()->change();
            $table->foreign('etudiant_id')->references('id')->on('etudiants')->nullOnDelete();

            // Ajouter colonne user_id pour employés
            $table->foreignId('user_id')
                ->nullable()
                ->after('etudiant_id')
                ->constrained('users')
                ->nullOnDelete();

            // Index optimisés
            $table->index(['user_id', 'detected_at']);
            $table->index(['status', 'severity', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    /**
     * Reverse: Restaure contraintes originales (NOT NULL).
     */
    public function down(): void
    {
        Schema::table('attendance_anomalies', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->dropForeign(['etudiant_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'detected_at']);
            $table->dropIndex(['status', 'severity', 'user_id']);
            
            // Restaurer NOT NULL (approximatif - nécessite backup avant migration)
            $table->foreignId('stage_id')
                  ->change()
                  ->constrained('stages')
                  ->cascadeOnDelete();
            
            $table->foreignId('etudiant_id')
                  ->change()
                  ->constrained('etudiants')
                  ->cascadeOnDelete();
            
            $table->dropColumn('user_id');
        });
    }
}
;