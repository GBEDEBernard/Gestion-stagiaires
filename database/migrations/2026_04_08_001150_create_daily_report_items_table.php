<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->string('work_type')->nullable();
            $table->text('description');
            $table->text('outcome')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->unsignedTinyInteger('progress_percent')->nullable();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->timestamps();

            $table->index(['daily_report_id', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_items');
    }
};
