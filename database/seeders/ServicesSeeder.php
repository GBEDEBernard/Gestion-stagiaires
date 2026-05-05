<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $services = [
            'Stage en maintenance informatique',
            'Stage en développement web',
            'Formation en cybersécurité',
            'Atelier réseaux et télécommunications',
            'Visite de chantiers BTP et énergie',
            'Formation en import-export et commerce',
            'Développement de solutions logicielles',
            'Installation et maintenance de réseaux',
            'Gestion de projets BTP',
            'Solutions énergétiques et électricité',
            'Formation continue et consulting',
            'Fourniture de matériels et équipements',
            'Services d’import-export',
        ];

        foreach ($services as $nom) {
         DB::table('services')->insert([
        'nom' => $nom,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}

    }
}
