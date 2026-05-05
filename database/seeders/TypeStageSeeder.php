<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeStage;

class TypeStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Si tu veux éviter les doublons
        $types = [
            ['code' => '003', 'libelle' => 'Professionnel'],
            ['code' => '004', 'libelle' => 'Académique'],
        ];

        foreach ($types as $type) {
            TypeStage::updateOrCreate(
                ['code' => $type['code']], // clé unique
                ['libelle' => $type['libelle']]
            );
        }
    }
}
