<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'Stage en maintenance informatique',
            'Stage en developpement web',
            'Formation en cybersecurite',
            'Atelier reseaux et telecommunications',
            'Visite de chantiers BTP et energie',
            'Formation en import-export et commerce',
            'Developpement de solutions logicielles',
            'Installation et maintenance de reseaux',
            'Gestion de projets BTP',
            'Solutions energetiques et electricite',
            'Formation continue et consulting',
            'Fourniture de materiels et equipements',
            'Services d import-export',
        ];

        foreach ($services as $nom) {
            Service::updateOrCreate(['nom' => $nom]);
        }
    }
}
