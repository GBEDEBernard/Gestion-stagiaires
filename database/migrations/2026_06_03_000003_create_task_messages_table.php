<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-003 / Phase 1 — Fil de discussion AU NIVEAU DE LA TÂCHE.
     * Timeline unifiée : messages (producteur / superviseur / admin),
     * jalons de rapport, et changements de statut.
     */
    public function up(): void
    {
        Schema::create('task_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // 'message' = saisi par un humain
            // 'report_jalon' = entrée système référençant un rapport du jour
            // 'status_change' = entrée système (changement de statut/progression)
            $table->string('type')->default('message');

            $table->text('body')->nullable();

            // Rapport référencé (pour les jalons) ou rattaché à un message.
            $table->foreignId('daily_report_id')->nullable()->constrained('daily_reports')->nullOnDelete();

            $table->timestamps();

            $table->index(['task_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_messages');
    }
};
