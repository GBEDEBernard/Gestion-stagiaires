<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Horaire de référence de l'entreprise, modifiable par l'administrateur.
 *
 * Il vivait en dur dans le code (08:00), puis en fichier de configuration —
 * dans les deux cas hors de portée de l'administrateur. Il appartient à la
 * base : changer l'heure d'arrivée est une décision de gestion, pas un
 * déploiement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_schedule_settings', function (Blueprint $table) {
            $table->id();
            $table->time('start_time')->default('08:00:00');
            $table->time('end_time')->default('18:00:00');
            $table->unsignedSmallInteger('break_minutes')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('work_schedule_settings')->insert([
            'start_time'    => '08:00:00',
            'end_time'      => '18:00:00',
            'break_minutes' => 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Les stages existants portent 08:30–17:30, hérité d'un défaut de
        // migration et jamais choisi par personne. L'horaire de référence est
        // 08:00–18:00 : on les y aligne, sans toucher à ceux qu'un
        // administrateur aurait volontairement réglés autrement.
        DB::table('stages')
            ->where('expected_check_in_time', '08:30:00')
            ->where('expected_check_out_time', '17:30:00')
            ->update([
                'expected_check_in_time'  => '08:00:00',
                'expected_check_out_time' => '18:00:00',
            ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedule_settings');
    }
};
