<?php

namespace Database\Seeders;

use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\User;
use App\Services\RolePermissionPresetService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $presetService = app(RolePermissionPresetService::class);

        $users = [
            [
                'name' => 'GBEDE Bernard',
                'email' => 'gbedebernard60@gmail.com',
                'password' => Hash::make('VisaBernard6142@'),
                'status' => 'actif',
                'role' => 'admin',
            ],
            [
                'name' => 'Utilisateur Test1',
                'email' => 'gbedebernard61@gmail.com',
                'password' => Hash::make('aqwzsxedc'),
                'status' => 'actif',
                'role' => 'etudiant',
            ],
            [
                'name' => 'Superviseur Test',
                'email' => 'superviseur@gst.local',
                'password' => Hash::make('Superviseur123!'),
                'status' => 'actif',
                'role' => 'superviseur',
            ],
        ];

        foreach ($users as $userData) {
            [$prenom, $nom] = $this->splitName($userData['name']);

            $personnel = Personnel::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'telephone' => null,
                    'genre' => null,
                    'date_naissance' => null,
                    'adresse' => null,
                    'created_by' => null,
                ]
            );

            if ($userData['role'] === 'etudiant') {
                $etudiant = Etudiant::updateOrCreate(
                    ['personnel_id' => $personnel->id],
                    ['ecole' => 'Compte test']
                );

                $personnel->update([
                    'personnable_type' => Etudiant::class,
                    'personnable_id' => $etudiant->id,
                ]);
            } else {
                $personnel->update([
                    'personnable_type' => null,
                    'personnable_id' => null,
                ]);
            }

            $userAttributes = [
                'password' => $userData['password'],
                'status' => $userData['status'],
                'email_verified_at' => Carbon::now(),
            ];

            if (Schema::hasColumn('users', 'name')) {
                $userAttributes['name'] = $userData['name'];
            }

            if (Schema::hasColumn('users', 'email')) {
                $userAttributes['email'] = $userData['email'];
            }

            $user = User::updateOrCreate(
                ['personnel_id' => $personnel->id],
                $userAttributes
            );

            $presetService->assignRolesAndPermissions(
                $user,
                [$userData['role']],
                $presetService->permissionsForRoles([$userData['role']])
            );
        }
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [
            $parts[0] ?? '',
            $parts[1] ?? $parts[0] ?? '',
        ];
    }
}
