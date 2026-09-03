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
        Schema::table('employes', function (Blueprint $table) {
            // Superviseur attitré pour l'employé (rôle superviseur)
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('personnel_id');
            $table->index('supervisor_id');

            $table->foreign('supervisor_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            // Supprimer la FK puis la colonne
            $table->dropForeign(['supervisor_id']);
            $table->dropIndex(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }
};

