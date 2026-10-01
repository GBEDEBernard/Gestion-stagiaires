<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chaîne de prolongation explicite.
 *
 * Jusqu'ici, deux stages d'un même étudiant étaient considérés comme une
 * prolongation implicite dès lors qu'ils partageaient domaine et type. Deux
 * stages distincts de la même filière, séparés par un an, étaient alors
 * confondus : une attestation unique pour une durée cumulée qui n'a jamais
 * eu lieu, et un historique qui ne disait plus quelle période était réelle.
 *
 * La colonne rend le lien explicite. Un stage prolonge un stage précis ; deux
 * stages simplement proches restent deux stages, avec deux attestations.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->foreignId('prolonge_stage_id')
                ->nullable()
                ->after('domaine_id')
                ->constrained('stages')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prolonge_stage_id');
        });
    }
};
