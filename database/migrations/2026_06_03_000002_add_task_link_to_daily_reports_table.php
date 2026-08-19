<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-003 / Phase 1 — Lien direct rapport ↔ tâche (une seule tâche par rapport)
     * + progression déclarée ce jour-là pour cette tâche.
     */
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->foreignId('task_id')
                ->nullable()
                ->after('attendance_day_id')
                ->constrained('tasks')
                ->nullOnDelete();

            $table->unsignedTinyInteger('task_progress_percent')
                ->nullable()
                ->after('completion_rate');

            $table->index(['task_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropIndex(['task_id', 'report_date']);
            $table->dropForeign(['task_id']);
            $table->dropColumn(['task_id', 'task_progress_percent']);
        });
    }
};
