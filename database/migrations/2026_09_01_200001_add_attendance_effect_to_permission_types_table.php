<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Déclare l'effet de chaque type de permission sur la présence.
 *
 * Les quatre types existants n'ont pas le même effet : une absence ou un congé
 * excusent des journées entières, alors qu'un retard ou un départ anticipé
 * excusent seulement un écart horaire sur une journée où la personne était bien
 * présente. Sans cette distinction, brancher les permissions sur les absences
 * effacerait des journées de présence réelles.
 *
 * Les dates vivent dans le JSON fields_data, dont les clés dépendent du type :
 * on les nomme ici plutôt que de les deviner.
 */
return new class extends Migration
{
    /** slug => [effet, clé de date de début, clé de date de fin] */
    private const EFFECTS = [
        'absence'            => ['excuses_day',              'start_date', 'end_date'],
        'conge-exceptionnel' => ['excuses_day',              'start_date', 'end_date'],
        'retard'             => ['excuses_late',             'date',       null],
        'depart-anticipe'    => ['excuses_early_departure',  'date',       null],
    ];

    public function up(): void
    {
        Schema::table('permission_types', function (Blueprint $table) {
            $table->string('attendance_effect', 32)->default('none')->after('fields_config');
            $table->string('date_from_field', 64)->nullable()->after('attendance_effect');
            $table->string('date_to_field', 64)->nullable()->after('date_from_field');
        });

        foreach (self::EFFECTS as $slug => [$effect, $from, $to]) {
            DB::table('permission_types')->where('slug', $slug)->update([
                'attendance_effect' => $effect,
                'date_from_field'   => $from,
                'date_to_field'     => $to,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('permission_types', function (Blueprint $table) {
            $table->dropColumn(['attendance_effect', 'date_from_field', 'date_to_field']);
        });
    }
};
