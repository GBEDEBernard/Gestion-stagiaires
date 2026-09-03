<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La pause de référence passe à deux heures.
 *
 * Elle valait 0 pour ne rien changer au comportement existant lors de son
 * introduction. C'est désormais le standard de l'entreprise, et le formulaire
 * de stage doit le proposer d'emblée.
 *
 * stages.break_minutes devient nullable : à 0 non nul, un stage écrasait
 * toujours la référence au lieu d'en hériter, et la valeur d'entreprise
 * n'atteignait jamais personne. NULL signifie « suit l'entreprise ».
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->unsignedSmallInteger('break_minutes')->nullable()->default(null)->change();
        });

        // Les stages portant encore le 0 d'origine n'ont jamais été réglés par
        // personne : on les fait hériter plutôt que de les figer à zéro.
        DB::table('stages')->where('break_minutes', 0)->update(['break_minutes' => null]);

        Schema::table('work_schedule_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('break_minutes')->default(120)->change();
        });

        DB::table('work_schedule_settings')->where('break_minutes', 0)->update(['break_minutes' => 120]);
    }

    public function down(): void
    {
        DB::table('stages')->whereNull('break_minutes')->update(['break_minutes' => 0]);

        Schema::table('stages', function (Blueprint $table) {
            $table->unsignedSmallInteger('break_minutes')->default(0)->change();
        });

        Schema::table('work_schedule_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('break_minutes')->default(0)->change();
        });
    }
};
