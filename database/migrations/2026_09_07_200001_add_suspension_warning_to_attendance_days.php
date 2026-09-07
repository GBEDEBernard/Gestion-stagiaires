<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Une journée clôturée d'office et jamais réclamée finissait par se perdre :
 * la modale revenait à chaque visite, et rien n'obligeait à y répondre.
 *
 * L'avertissement est daté pour qu'il ne parte qu'une fois, et pour que la
 * suspension du lendemain sache qu'il a bien été donné : on ne coupe l'accès
 * à personne sans l'avoir prévenu la veille.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_days', function (Blueprint $table) {
            $table->dateTime('suspension_warned_at')->nullable()->after('claimed_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_days', function (Blueprint $table) {
            $table->dropColumn('suspension_warned_at');
        });
    }
};
