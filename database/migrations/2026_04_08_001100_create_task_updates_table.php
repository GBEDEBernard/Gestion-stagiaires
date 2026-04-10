<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('daily_report_id')->nullable()->constrained('daily_reports')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->nullable();
            $table->unsignedTinyInteger('progress_percent')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('happened_at');
            $table->timestamps();

            $table->index(['task_id', 'happened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_updates');
    }
};
