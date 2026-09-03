<?php

namespace Database\Seeders;

use App\Models\PermissionType;
use Illuminate\Database\Seeder;

class PermissionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Absence',
                'slug' => 'absence',
                'icon' => 'calendar-x',
                'color' => 'red',
                'description' => "Demande d'absence pour une ou plusieurs journees",
                'fields_config' => [
                    ['key' => 'start_date', 'label' => 'Date de debut', 'type' => 'date', 'required' => true],
                    ['key' => 'end_date', 'label' => 'Date de fin', 'type' => 'date', 'required' => true],
                    ['key' => 'motif', 'label' => 'Motif detaille', 'type' => 'textarea', 'required' => true],
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Retard',
                'slug' => 'retard',
                'icon' => 'clock',
                'color' => 'amber',
                'description' => "Justification d'un retard a l'arrivee",
                'fields_config' => [
                    ['key' => 'date', 'label' => 'Date du retard', 'type' => 'date', 'required' => true],
                    ['key' => 'start_time', 'label' => "Heure d'arrivee prevue", 'type' => 'time', 'required' => true],
                    ['key' => 'end_time', 'label' => "Heure d'arrivee effective", 'type' => 'time', 'required' => true],
                    ['key' => 'motif', 'label' => 'Motif du retard', 'type' => 'textarea', 'required' => true],
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Depart anticipe',
                'slug' => 'depart-anticipe',
                'icon' => 'log-out',
                'color' => 'orange',
                'description' => "Permission de quitter avant l'heure officielle",
                'fields_config' => [
                    ['key' => 'date', 'label' => 'Date', 'type' => 'date', 'required' => true],
                    ['key' => 'departure_time', 'label' => 'Heure de depart souhaitee', 'type' => 'time', 'required' => true],
                    ['key' => 'motif', 'label' => 'Motif', 'type' => 'textarea', 'required' => true],
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'Conge exceptionnel',
                'slug' => 'conge-exceptionnel',
                'icon' => 'gift',
                'color' => 'purple',
                'description' => 'Demande de conge pour raison exceptionnelle',
                'fields_config' => [
                    ['key' => 'start_date', 'label' => 'Date de debut', 'type' => 'date', 'required' => true],
                    ['key' => 'end_date', 'label' => 'Date de fin', 'type' => 'date', 'required' => true],
                    ['key' => 'motif', 'label' => 'Motif et justification', 'type' => 'textarea', 'required' => true],
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($types as $type) {
            PermissionType::updateOrCreate(
                ['slug' => $type['slug']],
                $type + ['active' => true]
            );
        }
    }
}
