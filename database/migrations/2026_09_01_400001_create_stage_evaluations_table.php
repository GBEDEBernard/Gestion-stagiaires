<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * L'évaluation d'un stage : une par stage, en brouillon puis figée.
 *
 * attendance_snapshot conserve les ratios d'assiduité tels qu'ils étaient au
 * moment de la finalisation. Sans ce gel, une absence corrigée six mois plus
 * tard modifierait rétroactivement une note déjà communiquée à une école.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stage_id')->unique()->constrained('stages')->cascadeOnDelete();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status', 16)->default('draft'); // draft | finalized

            $table->text('general_comment')->nullable();

            // Niveau de divulgation des anomalies retenu pour le document remis.
            $table->string('anomaly_disclosure', 16)->default('count');

            $table->json('attendance_snapshot')->nullable();

            // Moyenne pondérée sur 20, figée à la finalisation.
            $table->decimal('final_score', 5, 2)->nullable();

            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('reopened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reopened_at')->nullable();

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_evaluations');
    }
};
