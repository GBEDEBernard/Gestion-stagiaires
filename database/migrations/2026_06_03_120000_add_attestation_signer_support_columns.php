<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Ajout des colonnes à la table users
        if (!Schema::hasColumn('users', 'is_signer')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_signer')->default(false)->after('status');
            });
        }

        if (!Schema::hasColumn('users', 'signataire_poste')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('signataire_poste')->nullable()->after('is_signer');
            });
        }

        if (!Schema::hasColumn('users', 'signataire_sigle')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('signataire_sigle', 10)->nullable()->after('signataire_poste');
            });
        }

        if (!Schema::hasColumn('users', 'signataire_ordre')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('signataire_ordre')->nullable()->after('signataire_sigle');
            });
        }

        if (!Schema::hasColumn('users', 'signataire_peut_par_ordre')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('signataire_peut_par_ordre')->default(false)->after('signataire_ordre');
            });
        }

        // Ajout de la colonne user_id à la table signataires
        if (!Schema::hasColumn('signataires', 'user_id')) {
            Schema::table('signataires', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('email')->constrained('users')->nullOnDelete();
            });
        }

        // Création de la permission
        Permission::firstOrCreate([
            'name' => 'signer_attestation',
            'guard_name' => 'web',
        ]);
    }

    public function down(): void
    {
        Permission::where('name', 'signer_attestation')->delete();

        // Supprimer les colonnes de users
        $columns = ['signataire_peut_par_ordre', 'signataire_ordre', 'signataire_sigle', 'signataire_poste', 'is_signer'];
        foreach ($columns as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        // Supprimer la colonne user_id de signataires
        if (Schema::hasColumn('signataires', 'user_id')) {
            Schema::table('signataires', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};