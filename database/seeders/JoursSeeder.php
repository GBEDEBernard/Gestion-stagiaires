<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jour;

class JoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $joursOuvrables = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

        foreach ($joursOuvrables as $jour) {
            Jour::firstOrCreate(['jour' => $jour]);
        }
    }
}
