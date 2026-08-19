<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Champs dynamiques de la fiche de poste rattachés au stage.
     */
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->string('intitule_poste')->nullable();
            $table->string('filiere')->nullable();
            $table->string('niveau_etude')->nullable();
            $table->string('tuteur_academique')->nullable();
            $table->string('indemnite')->nullable();
            $table->json('livrables')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn([
                'intitule_poste',
                'filiere',
                'niveau_etude',
                'tuteur_academique',
                'indemnite',
                'livrables',
            ]);
        });
    }
};
