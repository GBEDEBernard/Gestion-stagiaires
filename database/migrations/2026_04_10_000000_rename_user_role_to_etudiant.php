<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // jb -> On remplace ici le role technique "user" par le role
        // metier "etudiant" pour rendre l'administration plus claire.
        DB::table('roles')
            ->where('name', 'user')
            ->update(['name' => 'etudiant']);
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'etudiant')
            ->update(['name' => 'user']);
    }
};
