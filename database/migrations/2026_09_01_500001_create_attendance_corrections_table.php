<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Correction d'une heure d'arrivée mal enregistrée.
 *
 * Cas visé : la personne est arrivée à l'heure mais n'a pas pu scanner —
 * lecteur en panne, téléphone déchargé, GPS introuvable. Le retard constaté
 * n'est alors pas le sien.
 *
 * La journée est bien modifiée pour que les calculs en tiennent compte, mais
 * les valeurs d'origine sont conservées ici. La ponctualité entre dans la note
 * de stage : un chiffre corrigé sans trace serait indéfendable devant une école,
 * et une correction abusive resterait invisible.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();

            // Une seule correction en vigueur par journée : pour en poser une
            // autre il faut d'abord annuler la précédente, sinon l'heure
            // d'origine serait perdue au deuxième passage.
            $table->foreignId('attendance_day_id')->unique()->constrained('attendance_days')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->dateTime('original_check_in_at')->nullable();
            $table->string('original_arrival_status', 32)->nullable();
            $table->unsignedInteger('original_late_minutes')->default(0);

            // dateTime et non timestamp : MySQL refuse un TIMESTAMP NOT NULL
            // sans valeur par défaut quand explicit_defaults_for_timestamp est
            // désactivé, et rejette la création de la table.
            $table->dateTime('corrected_check_in_at');

            $table->text('reason');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
