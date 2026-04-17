<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->nullable()->constrained('stages')->nullOnDelete();
            $table->foreignId('etudiant_id')->constrained('etudiants')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('check_in_event_id')->nullable()->constrained('attendance_events')->nullOnDelete();
            $table->foreignId('check_out_event_id')->nullable()->constrained('attendance_events')->nullOnDelete();
            $table->date('attendance_date');
            $table->timestamp('first_check_in_at')->nullable();
            $table->timestamp('last_check_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_departure_minutes')->default(0);
            $table->unsignedSmallInteger('anomaly_count')->default(0);
            $table->string('day_status')->default('pending');
            $table->string('validation_status')->default('pending');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('summary_notes')->nullable();
            $table->timestamps();

            $table->unique(['stage_id', 'attendance_date']);
            $table->index(['etudiant_id', 'attendance_date']);
            $table->index(['validation_status', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_days');
    }
};
