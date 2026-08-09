<?php

namespace Database\Seeders;

use App\Models\Ecole;
use Illuminate\Database\Seeder;

class EcoleSeeder extends Seeder
{
    /**
     * Écoles de provenance prédéfinies (modifiables par l'admin).
     */
    public function run(): void
    {
        $ecoles = [
            'Université d\'Abomey-Calavi (UAC)',
            'Université de Parakou (UP)',
            'Université Nationale d\'Agriculture (UNA)',
            'École Polytechnique d\'Abomey-Calavi (EPAC)',
            'École Nationale d\'Économie Appliquée et de Management (ENEAM)',
            'École Nationale Supérieure des Travaux Publics (ENSTP)',
            'Institut de Formation et de Recherche en Informatique (IFRI)',
            'École Supérieure de Gestion d\'Informatique et des Sciences (ESGIS)',
            'École Supérieure de Génie Civil (ESGC)',
            'Institut Supérieur de Technologie de Cotonou (IST)',
            'École Supérieure de Management (ESM)',
            'Institut Supérieur d\'Informatique et de Gestion (ISIG)',
        ];

        foreach ($ecoles as $nom) {
            Ecole::firstOrCreate(['nom' => $nom]);
        }
    }
}