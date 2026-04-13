<?php

namespace Database\Seeders;

use App\Models\SiteGeofence;
use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteGeofenceSeeder extends Seeder
{
    public function run(): void
    {
        // Créer site TFG SARL si n'existe pas
        $tfgSite = Site::firstOrCreate(
            ['name' => 'TFG SARL'],
            [
                'code'      => 'TFG-HQ',
                'address'   => 'Siège TFG SARL, Benin',
                'is_active' => true,
            ]
        );

        // Supprimer anciens geofences TFG si existants
        SiteGeofence::where('site_id', $tfgSite->id)->delete();

        // ─────────────────────────────────────────────────────────────────────
        // Geofence principal TFG SARL
        //
        // POURQUOI 100m / 50m ?
        //   - GPS en intérieur (bureau, bâtiment béton) : précision typique 20–80m
        //   - GPS en extérieur proche bâtiment           : précision typique 5–20m
        //   - Un rayon de 5m est uniquement viable en plein air avec signal fort
        //   - 100m couvre le bâtiment + parking sans autoriser le quartier entier
        //   - allowed_accuracy_meters = tolérance sur l'imprécision du capteur GPS
        // ─────────────────────────────────────────────────────────────────────
        SiteGeofence::create([
            'site_id'                => $tfgSite->id,
            'name'                   => 'TFG SARL - Zone Principale',
            'center_latitude'        => 6.424759441415669,
            'center_longitude'       => 2.317200378309422,
            'radius_meters'          => 100,  // ✅ était 5m → trop strict pour GPS mobile en bureau
            'allowed_accuracy_meters'=> 50,   // ✅ était 10m → GPS intérieur = 20-80m typique
            'is_primary'             => true,
            'is_active'              => true,
            'notes'                  => 'Zone pointage principale TFG SARL. 100m rayon + 50m tolérance précision GPS (bureau intérieur).',
        ]);

        // Geofence secondaire étendu (pour cas très dégradé / signal faible)
        SiteGeofence::create([
            'site_id'                => $tfgSite->id,
            'name'                   => 'TFG SARL - Zone Étendue (signal faible)',
            'center_latitude'        => 6.424759441415669,
            'center_longitude'       => 2.317200378309422,
            'radius_meters'          => 200,
            'allowed_accuracy_meters'=> 80,
            'is_primary'             => false,
            'is_active'              => true,
            'notes'                  => 'Zone secondaire pour GPS très imprécis (sous-sol, salle sans fenêtre).',
        ]);

        $this->command->info('✅ Geofences TFG SARL créés avec rayons réalistes: site_id=' . $tfgSite->id);
        $this->command->info('   → Primaire  : 100m rayon / 50m tolérance GPS');
        $this->command->info('   → Secondaire: 200m rayon / 80m tolérance GPS');
        $this->command->line('');
        $this->command->warn('⚠️  Pour appliquer: php artisan db:seed --class=SiteGeofenceSeeder');
    }
}