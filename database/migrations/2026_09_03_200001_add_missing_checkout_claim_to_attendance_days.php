<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Départ oublié : ce que la personne déclare le lendemain.
 *
 * La journée est clôturée automatiquement à l'heure de fin prévue — jamais
 * plus, sinon oublier rapporterait davantage que pointer. Reste à savoir ce
 * qui s'est réellement passé : la personne déclare une heure et un motif, et
 * cela s'arrête là. Rien n'est appliqué sur sa seule parole ; l'administrateur
 * tranche et pose l'heure lui-même.
 *
 * L'état vit dans `departure_status`, colonne créée à l'origine et restée
 * inutilisée :
 *   null          — départ pointé normalement
 *   auto_closed   — clôturé d'office, rien de déclaré
 *   claimed       — la personne a déclaré une heure, en attente de l'admin
 *   corrected     — l'admin a tranché
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_days', function (Blueprint $table) {
            // dateTime et non timestamp : MySQL refuse un TIMESTAMP NOT NULL
            // sans valeur par défaut selon explicit_defaults_for_timestamp.
            $table->dateTime('claimed_check_out_at')->nullable()->after('departure_status');
            $table->text('claimed_check_out_reason')->nullable()->after('claimed_check_out_at');
            $table->dateTime('claimed_at')->nullable()->after('claimed_check_out_reason');

            $table->index(['departure_status', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::table('attendance_days', function (Blueprint $table) {
            $table->dropIndex(['departure_status', 'attendance_date']);
            $table->dropColumn(['claimed_check_out_at', 'claimed_check_out_reason', 'claimed_at']);
        });
    }
};
