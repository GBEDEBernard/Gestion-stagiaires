<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')
                  ->constrained('etudiants')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreignId('typestage_id')
                  ->nullable()
                  ->constrained('typestages')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('service_id')
                  ->nullable()
                  ->constrained('services')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('badge_id')
                  ->nullable()
                  ->constrained('badges')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
            $table->string('theme')->nullable();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->foreignId('site_id')->nullable();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->time('expected_check_in_time')->nullable();
            $table->time('expected_check_out_time')->nullable();
            $table->unsignedInteger('allowed_late_minutes')->default(15);
            $table->unsignedInteger('allowed_early_departure_minutes')->default(15);
            $table->string('presence_mode')->default('geolocation_only');
            $table->string('follow_up_status')->default('active');
            $table->foreignId('domaine_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
