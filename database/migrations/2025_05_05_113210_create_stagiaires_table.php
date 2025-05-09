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
    Schema::create('stagiaires', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->string('prenom');
        $table->string('email')->unique(); // si chaque email doit être unique
        $table->string('telephone')->unique();
        $table->foreignId('typestage_id')->constrained()->onDelete('cascade');
        $table->foreignId('badge_id')->constrained()->onDelete('cascade'); 
        $table->string('ecole');
        $table->string('theme')->nullable();
        $table->date('date_debut');
        $table->date('date_fin');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};
