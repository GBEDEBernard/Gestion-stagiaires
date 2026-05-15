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
                'prenom' => 'Employé',
                'email' => 'employe.tfg@tfg.local',
                'domaine_id' => $tfg?->id,
                'site_id' => 1, // à ajuster selon votre site par défaut
                'matricule' => 'EMP-TFG-001',
            ],
            [
                'nom' => 'EPAC',
                'prenom' => 'Employé',
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

            // Créer l'employé
            $employe = Employe::create([
                'domaine_id' => $data['domaine_id'],
                'site_id' => $data['site_id'],
                'matricule' => $data['matricule'],
                'poste' => null,
            ]);

            // Créer le personnel lié
            $personnel = Personnel::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => null,
                'genre' => null,
                'personnable_type' => Employe::class,
                'personnable_id' => $employe->id,
                'created_by' => null,
            ]);

            // Créer le compte utilisateur lié
            $user = User::create([
                'personnel_id' => $personnel->id,
                'password' => Hash::make('Password123!'),
                'status' => 'actif',
                'must_change_password' => true,
                'temporary_password_created_at' => now(),
                'email_verified_at' => now(),
            ]);

            $presetService->ensureRoleDefaults($user, ['employe']);
        }
    }
}