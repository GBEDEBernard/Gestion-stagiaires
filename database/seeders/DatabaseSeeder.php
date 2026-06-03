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
            ServicesSeeder::class,
            JoursSeeder::class,
            TypeStageSeeder::class,
            UserSeeder::class,
            SignatairesSeeder::class,
            DomaineSiteSeeder::class,
            EmployeSeeder::class,
            EtudiantsPresenceSeeder::class,
            SiteGeofenceSeeder::class,

        ]);
    }
}
