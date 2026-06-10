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
<<<<<<< HEAD
=======
            PermissionTypeSeeder::class,
            SignatairesSeeder::class,
>>>>>>> 49a0902eb66d18e67c5b0d41cf47d7f6493fbe63
=======
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
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
