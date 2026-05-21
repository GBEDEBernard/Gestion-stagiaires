<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            if (Schema::hasColumn('etudiants', 'personnel_id')) {
                $table->dropForeign(['personnel_id']);
                $table->foreign('personnel_id')
                    ->references('id')
                    ->on('personnels')
                    ->onDelete('cascade');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'personnel_id')) {
                $table->dropForeign(['personnel_id']);
                $table->foreign('personnel_id')
                    ->references('id')
                    ->on('personnels')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            if (Schema::hasColumn('etudiants', 'personnel_id')) {
                $table->dropForeign(['personnel_id']);
                $table->foreign('personnel_id')
                    ->references('id')
                    ->on('personnels');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'personnel_id')) {
                $table->dropForeign(['personnel_id']);
                $table->foreign('personnel_id')
                    ->references('id')
                    ->on('personnels');
            }
        });
    }
};
