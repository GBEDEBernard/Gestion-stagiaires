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
        // ─────────────────────────────────────────────────────────────────────
        // TFG CENTRE - Position EXACTE demandée (rayon STRICT 25m)
        // Coordonnées: 6.408685590450129, 2.3305279419712015
        // Rayon: 25m (20-30m milieu), Accuracy tolérance: 30m GPS mobile réaliste
        // ─────────────────────────────────────────────────────────────────────
        SiteGeofence::create([
            'site_id'                => $tfgSite->id,
            'name'                   => 'TFG Centre - Zone Principale (25m)',
            'center_latitude'        => 6.408685590450129,
            'center_longitude'       => 2.3305279419712015,
            'radius_meters'          => 25,
            'allowed_accuracy_meters' => 30,
            'is_primary'             => true,
            'is_active'              => true,
            'notes'                  => 'TFG Centre exact. Rayon 25m strict. Tolérance GPS 30m. Employés/étudiants OK.',
        ]);

        // ─────────────────────────────────────────────────────────────────────
        // TFG SARL Ancienne zone (backup 100m)
        // ─────────────────────────────────────────────────────────────────────
        SiteGeofence::create([
            'site_id'                => $tfgSite->id,
            'name'                   => 'TFG SARL - Zone Backup (100m)',
            'center_latitude'        => 6.424759441415669,
            'center_longitude'       => 2.317200378309422,
            'radius_meters'          => 100,
            'allowed_accuracy_meters' => 50,
            'is_primary'             => false,
            'is_active'              => true,
            'notes'                  => 'Ancienne zone TFG (backup signal faible).',
        ]);

        // Geofence secondaire étendu (pour cas très dégradé / signal faible)
        SiteGeofence::create([
            'site_id'                => $tfgSite->id,
            'name'                   => 'TFG SARL - Zone Étendue (signal faible)',
            'center_latitude'        => 6.424759441415669,
            'center_longitude'       => 2.317200378309422,
            'radius_meters'          => 200,
            'allowed_accuracy_meters' => 80,
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
