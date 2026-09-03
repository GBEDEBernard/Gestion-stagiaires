<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->nullable()->unique();
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('telephone')->nullable();
            $table->string('ecole')->nullable();
            $table->string('genre')->default('Masculin');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etudiants');
    }
};
