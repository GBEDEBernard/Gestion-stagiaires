<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attestation_signataire', function (Blueprint $table) {
            $table->id();

            // FK vers attestation
            $table->foreignId('attestation_id')->constrained('attestations')->cascadeOnDelete();

            // FK vers signataire
            $table->foreignId('signataire_id')->constrained('signataires')->cascadeOnDelete();

            // Pour savoir si le signataire signe "par ordre"
            $table->boolean('par_ordre')->default(false);

            // Position d’affichage si plusieurs signataires
            $table->unsignedInteger('ordre')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attestation_signataire');
    }
};
