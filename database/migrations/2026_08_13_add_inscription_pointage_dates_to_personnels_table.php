<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->date('date_inscription')->nullable()->after('adresse');
            $table->date('date_debut_pointage')->nullable()->after('date_inscription');
        });

        // Backfill : la date d'inscription des personnels existants = leur date de création.
        // date_debut_pointage reste NULL → la logique d'absence retombe sur le comportement actuel.
        DB::table('personnels')
            ->whereNull('date_inscription')
            ->update(['date_inscription' => DB::raw('DATE(created_at)')]);
    }

    public function down()
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropColumn(['date_inscription', 'date_debut_pointage']);
        });
    }
};