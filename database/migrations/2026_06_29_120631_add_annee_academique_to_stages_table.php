<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->string('annee_academique', 9)->nullable()->after('duree_mois');
        });

        DB::table('stages')->whereNull('annee_academique')->orderBy('id')->each(function ($stage) {
            $mois = date('n', strtotime($stage->date_debut));
            $annee = date('Y', strtotime($stage->date_debut));
            if ($mois >= 9) {
                $anneeAcademique = $annee . '-' . ($annee + 1);
            } else {
                $anneeAcademique = ($annee - 1) . '-' . $annee;
            }
            DB::table('stages')->where('id', $stage->id)->update(['annee_academique' => $anneeAcademique]);
        });
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn('annee_academique');
        });
    }
};
