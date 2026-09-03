<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('genre')->nullable();
            $table->date('date_naissance')->nullable();
            $table->text('adresse')->nullable();
            // Polymorphique : lien vers Etudiant ou Employe
            $table->string('personnable_type')->nullable();
            $table->unsignedBigInteger('personnable_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('personnel_id')
                ->references('id')
                ->on('personnels')
                ->cascadeOnDelete();
        });

        Schema::table('etudiants', function (Blueprint $table) {
            $table->foreign('personnel_id')
                ->references('id')
                ->on('personnels')
                ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('etudiants', function (Blueprint $table) {
            $table->dropForeign(['personnel_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['personnel_id']);
        });

        Schema::dropIfExists('personnels');
    }
};
