<?php

namespace Database\Seeders;

use App\Models\Domaine;
use App\Models\Site;
use App\Models\SiteGeofence;
use App\Models\User;
use Illuminate\Database\Seeder;

class DomaineSiteSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first() ?? User::first();

        $tfg = Domaine::updateOrCreate(
            ['nom' => 'TFG'],
            ['description' => 'Domaine employé TFG', 'created_by' => $admin->id]
        );

        $epac = Domaine::updateOrCreate(
            ['nom' => 'EPAC'],
            ['description' => 'Domaine employé EPAC', 'created_by' => $admin->id]
        );

        $tfgSite = Site::updateOrCreate(
            ['code' => 'TFG-EMP'],
            [
                'name' => 'TFG SARL',
                'address' => 'TFG SARL',
                'city' => 'Cotonou',
                'country' => 'Benin',
                'latitude' => '6.418712640502904',
                'longitude' => '2.3062906009427055',
                'is_active' => true,
            ]
        );

        $epacSite = Site::updateOrCreate(
            ['code' => 'EPAC-EMP'],
            [
                'name' => 'EPAC',
                'address' => 'EPAC',
                'city' => 'Cotonou',
                'country' => 'Benin',
                'latitude' => '6.415114695144997',
                'longitude' => '2.3421014432729557',
                'is_active' => true,
            ]
        );

        $tfg->sites()->syncWithoutDetaching($tfgSite->id);
        $epac->sites()->syncWithoutDetaching($epacSite->id);

        SiteGeofence::updateOrCreate([
            'site_id' => $tfgSite->id,
            'name' => 'TFG - Zone de présence',
        ], [
            'center_latitude' => '6.418712640502904',
            'center_longitude' => '2.3062906009427055',
            'radius_meters' => 100,
            'allowed_accuracy_meters' => 60,
            'is_primary' => true,
            'is_active' => true,
            'notes' => 'Geofence principale du domaine TFG.',
        ]);

        SiteGeofence::updateOrCreate([
            'site_id' => $epacSite->id,
            'name' => 'EPAC - Zone de présence',
        ], [
            'center_latitude' => '6.415114695144997',
            'center_longitude' => '2.3421014432729557',
            'radius_meters' => 100,
            'allowed_accuracy_meters' => 60,
            'is_primary' => true,
            'is_active' => true,
            'notes' => 'Geofence principale du domaine EPAC.',
        ]);
    }
}
