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
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
