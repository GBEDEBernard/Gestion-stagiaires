<?php

namespace Database\Seeders;

use App\Models\Domaine;
use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeSeeder extends Seeder
{
    public function run(RolePermissionPresetService $presetService): void
    {
        $tfg = Domaine::firstWhere('nom', 'TFG');
        $epac = Domaine::firstWhere('nom', 'EPAC');

        $employees = [
            [
                'name' => 'Employé TFG',
                'email' => 'employe.tfg@tfg.local',
                'domaine_id' => $tfg?->id,
            ],
            [
                'name' => 'Employé EPAC',
                'email' => 'employe.epac@epac.local',
                'domaine_id' => $epac?->id,
            ],
        ];

        foreach ($employees as $data) {
            if (!$data['domaine_id']) {
                continue;
            }

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('Password123!'),
                    'status' => 'actif',
                    'email_verified_at' => now(),
                    'domaine_id' => $data['domaine_id'],
                ]
            );

            $presetService->ensureRoleDefaults($user, ['employe']);
        }
    }
}
