<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marque le passage de la composition de la grille à la saisie des notes.
 *
 * Tant que c'est NULL, l'écran d'évaluation montre le tableau des critères,
 * modifiable ligne par ligne. Une fois posé, la colonne « Note » apparaît.
 * Réversible : l'administrateur peut revenir composer la grille.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage_evaluations', function (Blueprint $table) {
            $table->timestamp('grid_validated_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('stage_evaluations', function (Blueprint $table) {
            $table->dropColumn('grid_validated_at');
        });
    }
};
