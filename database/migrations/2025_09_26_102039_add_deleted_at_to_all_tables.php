<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // liste toutes tes tables
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
            
            // 👉 ajoute ici toutes tes tables !
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                    $table->softDeletes();
                }
            });
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

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
