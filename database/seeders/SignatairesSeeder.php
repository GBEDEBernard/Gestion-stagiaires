<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Signataire;
use Illuminate\Support\Facades\Schema;

class SignatairesSeeder extends Seeder
{
    public function run()
    {
        // Désactiver les contraintes
        Schema::disableForeignKeyConstraints();
        Signataire::truncate();
        Schema::enableForeignKeyConstraints();

        // Réinsérer les données
        Signataire::insert([
            [
                'nom' => 'Appolinaire KONNON',
                'poste' => 'Directeur Général',
                'sigle' => 'DG',
                'ordre' => 1,
                'peut_par_ordre' => false,
            ],
            [
                'nom' => 'Gamaliel GBETIE',
                'poste' => 'Directeur Technique',
                'sigle' => 'DT',
                'ordre' => 2,
                'peut_par_ordre' => true,
            ],
            [
                'nom' => 'Mario AGBELESSESSI',
                'poste' => 'Directeur Technique Adjoint',
                'sigle' => 'DTA',
                'ordre' => 3,
                'peut_par_ordre' => true,
            ],
        ]);
    }
}
