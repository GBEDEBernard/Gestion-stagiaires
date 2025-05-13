<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->unique();
            $table->foreignId('typestage_id')->constrained('typestages')->onDelete('cascade');
            $table->foreignId('badge_id')->constrained('badges')->onDelete('restrict');
            $table->string('ecole')->nullable();
            $table->string('theme')->nullable();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->timestamps();
        });

        Schema::create('jour_stagiaire', function (Blueprint $table) {
            $table->foreignId('stagiaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('jour_id')->constrained()->onDelete('cascade');
            $table->primary(['stagiaire_id', 'jour_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('jour_stagiaire');
        Schema::dropIfExists('stagiaires');
    }
};
