<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
    public function up(): void
    {
        Schema::create('signataires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('poste'); // Ex: "Directeur Général"
            $table->string('sigle'); // Ex: "DG", "DT"
            $table->boolean('ordre')->default(false); // true si on doit afficher l’ordre
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signataires');
    }
};
