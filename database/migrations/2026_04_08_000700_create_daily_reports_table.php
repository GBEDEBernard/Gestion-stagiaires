<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->foreignId('attendance_day_id')->nullable()->constrained('attendance_days')->nullOnDelete();
            $table->date('report_date');
            $table->string('title')->nullable();
            $table->text('summary');
            $table->text('blockers')->nullable();
            $table->text('next_steps')->nullable();
            $table->decimal('hours_declared', 5, 2)->default(0);
            $table->unsignedTinyInteger('completion_rate')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('supervisor_comment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['stage_id', 'report_date']);
            $table->index(['etudiant_id', 'report_date']);
            $table->index(['status', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
