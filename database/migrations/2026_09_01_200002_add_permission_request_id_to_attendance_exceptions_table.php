<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rattache une exception de présence à la permission qui l'a produite.
 *
 * Permet de distinguer les jours excusés automatiquement de ceux saisis à la
 * main par un administrateur, et de retirer exactement les bons jours si une
 * permission est annulée après approbation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_exceptions', function (Blueprint $table) {
            $table->foreignId('permission_request_id')
                ->nullable()
                ->after('created_by')
                ->constrained('permission_requests')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_exceptions', function (Blueprint $table) {
            $table->dropForeign(['permission_request_id']);
            $table->dropColumn('permission_request_id');
        });
    }
};
