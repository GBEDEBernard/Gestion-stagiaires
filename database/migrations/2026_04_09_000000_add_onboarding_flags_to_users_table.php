<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // jb -> Ces colonnes decrivent l'etat d'appropriation du compte:
            // compte temporaire, date de creation du mot de passe provisoire
            // et date du premier mot de passe personnel.
            $table->boolean('must_change_password')->default(false)->after('password');
            $table->timestamp('temporary_password_created_at')->nullable()->after('must_change_password');
            $table->timestamp('password_changed_at')->nullable()->after('temporary_password_created_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'must_change_password',
                'temporary_password_created_at',
                'password_changed_at',
            ]);
        });
    }
};
