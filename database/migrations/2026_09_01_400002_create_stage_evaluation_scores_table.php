<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Une note par critère.
 *
 * label_snapshot et weight_snapshot ne sont pas de la dénormalisation
 * paresseuse : le jour où l'administrateur renomme un critère ou change un
 * coefficient, une note déjà remise ne doit pas bouger d'un chiffre. Une note
 * de stage est un document signé, elle doit rester lisible telle qu'elle a été
 * rendue, des années après.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_evaluation_scores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stage_evaluation_id')->constrained('stage_evaluations')->cascadeOnDelete();

            // Le critère peut disparaître du référentiel sans emporter la note.
            $table->foreignId('criterion_id')->nullable()->constrained('evaluation_criteria')->nullOnDelete();

            $table->string('label_snapshot');
            $table->unsignedSmallInteger('weight_snapshot')->default(1);
            $table->boolean('is_auto')->default(false);

            // Ce que le système a calculé, et ce que l'humain a retenu. Garder
            // les deux permet d'imprimer l'écart et sa justification.
            $table->decimal('computed_score', 5, 2)->nullable();
            $table->decimal('score', 5, 2)->nullable();

            $table->text('comment')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['stage_evaluation_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_evaluation_scores');
    }
};
