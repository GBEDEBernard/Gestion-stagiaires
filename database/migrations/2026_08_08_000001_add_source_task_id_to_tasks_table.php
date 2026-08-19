<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-007 — Assignation multi-personnes (ADMIN uniquement).
     * L'admin assigne la même tâche à plusieurs personnes : chacune reçoit sa
     * propre copie de la tâche (progression/rapports indépendants) reliée à la
     * tâche source par `source_task_id` (permettant la déduplication).
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('source_task_id')->nullable()->after('id')
                ->constrained('tasks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['source_task_id']);
            $table->dropColumn('source_task_id');
        });
    }
};