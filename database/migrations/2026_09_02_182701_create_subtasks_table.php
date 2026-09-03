<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subtasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();

            // Utilisateur assigné à cette sous-tâche (1 seul par sous-tâche).
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();

            // Ordre d'affichage.
            $table->unsignedSmallInteger('display_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['task_id', 'is_completed']);
            $table->index(['task_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subtasks');
    }
};
