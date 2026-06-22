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
            PermissionTypeSeeder::class,
            JoursSeeder::class,
            TypeStageSeeder::class,
            DomaineSiteSeeder::class,
            UserSeeder::class,
            SignatairesSeeder::class,
            EmployeSeeder::class,
            EtudiantsPresenceSeeder::class,
            SiteGeofenceSeeder::class,

        ]);
    }
}
