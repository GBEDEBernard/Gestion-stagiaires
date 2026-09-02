<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Référentiel des critères d'évaluation de stage.
 *
 * La grille est composée, pas dupliquée : un critère rattaché à un type de stage
 * ne vaut que pour lui, un critère sans type est commun à toutes les grilles.
 * Sans cela, l'administrateur devrait ressaisir « Assiduité » pour chaque type,
 * et deux grilles finiraient par diverger sur un critère censé être le même.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_criteria', function (Blueprint $table) {
            $table->id();

            // NULL = critère commun à tous les types de stage.
            $table->foreignId('typestage_id')
                ->nullable()
                ->constrained('typestages')
                ->cascadeOnDelete();

            $table->string('label');
            $table->text('description')->nullable();

            // Coefficient dans la moyenne pondérée. 1 partout = moyenne simple,
            // ce qui garde le système compréhensible tant qu'on n'y touche pas.
            $table->unsignedSmallInteger('weight')->default(1);

            // Critère calculé par le système plutôt que saisi à la main.
            $table->boolean('is_auto')->default(false);
            $table->string('auto_source', 32)->nullable();

            // Retrait sans perte : un critère désactivé disparaît des nouvelles
            // grilles sans toucher aux évaluations déjà rendues.
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['typestage_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_criteria');
    }
};
