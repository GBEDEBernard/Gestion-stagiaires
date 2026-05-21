<?php

namespace Database\Seeders;

use App\Models\Domaine;
use App\Models\Employe;
use App\Models\Personnel;
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
                'nom' => 'TFG',
                'prenom' => 'Employe',
                'email' => 'employe.tfg@tfg.local',
                'domaine_id' => $tfg?->id,
                'site_id' => 1,
                'matricule' => 'EMP-TFG-001',
            ],
            [
                'nom' => 'EPAC',
                'prenom' => 'Employe',
                'email' => 'employe.epac@epac.local',
                'domaine_id' => $epac?->id,
                'site_id' => 1,
                'matricule' => 'EMP-EPAC-001',
            ],
        ];

        foreach ($employees as $data) {
            if (!$data['domaine_id']) {
                continue;
            }

            $employe = Employe::updateOrCreate(
                ['matricule' => $data['matricule']],
                [
                    'domaine_id' => $data['domaine_id'],
                    'site_id' => $data['site_id'],
                    'poste' => null,
                ]
            );

            $personnel = Personnel::updateOrCreate(
                ['email' => $data['email']],
                [
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'telephone' => null,
                    'genre' => null,
                    'personnable_type' => Employe::class,
                    'personnable_id' => $employe->id,
                    'created_by' => null,
                ]
            );

            $user = User::updateOrCreate(
                ['personnel_id' => $personnel->id],
                [
                    'password' => Hash::make('Password123!'),
                    'status' => 'actif',
                    'must_change_password' => true,
                    'temporary_password_created_at' => now(),
                    'email_verified_at' => now(),
                ]
            );

            $presetService->ensureRoleDefaults($user, ['employe']);
        }
    }
}
