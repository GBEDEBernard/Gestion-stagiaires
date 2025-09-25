<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // D'abord créer les rôles et permissions
        $this->call([
            RolePermissionSeeder::class,
            SignatairesSeeder::class,
        ]);

       
    }
}