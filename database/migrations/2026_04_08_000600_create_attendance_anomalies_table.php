<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_event_id')->nullable()->constrained('attendance_events')->nullOnDelete();
            $table->foreignId('attendance_day_id')->nullable()->constrained('attendance_days')->nullOnDelete();
            $table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('anomaly_type');
            $table->string('severity')->default('medium');
            $table->string('status')->default('open');
            $table->timestamp('detected_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('resolution_note')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['status', 'severity']);
            $table->index(['stage_id', 'detected_at']);
            $table->index(['etudiant_id', 'detected_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_anomalies');
    }
};
