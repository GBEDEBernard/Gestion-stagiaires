<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * T-008 — La multi-assignation passe par la table pivot task_assignee :
     * la colonne source_task_id (copies T-007) n'est plus utilisée → supprimée.
     */
    public function up(): void
    {
        if (Schema::hasColumn('tasks', 'source_task_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['source_task_id']);
                $table->dropColumn('source_task_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('tasks', 'source_task_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('source_task_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('tasks')
                    ->nullOnDelete();
            });
        }
    }
};