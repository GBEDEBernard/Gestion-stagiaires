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
<<<<<<< HEAD
=======
            PermissionTypeSeeder::class,
            SignatairesSeeder::class,
>>>>>>> 49a0902eb66d18e67c5b0d41cf47d7f6493fbe63
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
