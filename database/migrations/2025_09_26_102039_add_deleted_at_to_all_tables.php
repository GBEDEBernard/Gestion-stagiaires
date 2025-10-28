<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tables = [
            'users',
            'stages',
            'services',
            'typestages',
            'badges',
            'contacts',
            'jours',
            'etudiants',
            'attestations',
            'activities',
            'stage_jour',
            'roles',
            'permissions',
            'signataires',
        ];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl)) { // ⚡ vérifie si la table existe
                Schema::table($tbl, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                        $table->softDeletes();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'users',
            'stages',
            'services',
            'typestages',
            'badges',
            'contacts',
            'jours',
            'etudiants',
            'attestations',
            'activities',
            'stage_jour',
            'roles',
            'permissions',
            'signataires',
        ];

        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl)) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
