<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->nullable()->constrained('stages')->nullOnDelete();
            $table->foreignId('etudiant_id')->nullable()->constrained('etudiants')->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('site_geofence_id')->nullable()->constrained('site_geofences')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('trusted_device_id')->nullable()->constrained('trusted_devices')->nullOnDelete();
            $table->string('event_type');
            $table->string('status')->default('pending');
            $table->timestamp('occurred_at');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('accuracy_meters')->nullable();
            $table->unsignedInteger('distance_to_site_meters')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->string('reason_code')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['stage_id', 'occurred_at']);
            $table->index(['etudiant_id', 'event_type', 'occurred_at']);
            $table->index(['status', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_events');
    }
};
