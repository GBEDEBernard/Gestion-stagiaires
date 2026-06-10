<?php

namespace Database\Seeders;

use App\Models\Employe;
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
            // ── Votre compte admin ──
            [
                'prenom'    => 'Bernard',
                'nom'       => 'GBEDE',
                'email'     => 'gbedebernard60@gmail.com',
                'password'  => Hash::make('VisaBernard6142@'),
                'status'    => 'actif',
                'role'      => 'admin',
                'is_signer' => false,
                'poste'     => 'Administrateur Système',
            ],

            // ── ID 2 — DG (Directeur Général) ──
            [
                'prenom'    => 'Appolinaire',
                'nom'       => 'KONNON',
                'email'     => 'konnon@tfg.bj',
                'password'  => Hash::make('DG_TFG_2025@'),
                'status'    => 'actif',
                'role'      => 'admin',
                'is_signer' => true,
                'poste'     => 'Directeur Général',
            ],

            // ── ID 3 — DT (Directeur Technique) ──
            [
                'prenom'    => 'Gamaliel',
                'nom'       => 'GBETIE',
                'email'     => 'gbetie@tfg.bj',
                'password'  => Hash::make('DT_TFG_2025@'),
                'status'    => 'actif',
                'role'      => 'admin',
                'is_signer' => true,
                'poste'     => 'Directeur Technique',
            ],

            // ── ID 4 — DTA (Directeur Technique Adjoint) ──
            [
                'prenom'    => 'Mario',
                'nom'       => 'AGBELESSESSI',
                'email'     => 'agbelessessi@tfg.bj',
                'password'  => Hash::make('DTA_TFG_2025@'),
                'status'    => 'actif',
                'role'      => 'admin',
                'is_signer' => true,
                'poste'     => 'Directeur Technique Adjoint',
            ],
        ];

        foreach ($users as $index => $userData) {
            // 1. Personnel
            $personnel = Personnel::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'nom'            => $userData['nom'],
                    'prenom'         => $userData['prenom'],
                    'telephone'      => null,
                    'genre'          => null,
                    'date_naissance' => null,
                    'adresse'        => null,
                    'created_by'     => null,
                ]
            );

            // 2. Employe
            $employe = Employe::updateOrCreate(
                ['personnel_id' => $personnel->id],
                [
                    'poste'     => $userData['poste'],
                    'matricule' => 'TFG-' . strtoupper(substr($userData['nom'], 0, 3)) . '-' . str_pad(($index + 1), 3, '0', STR_PAD_LEFT),
                    'domaine_id' => 1,
                    'site_id'    => 1,
                ]
            );

            $personnel->update([
                'personnable_type' => Employe::class,
                'personnable_id'   => $employe->id,
            ]);

<<<<<<< HEAD
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
=======
            // 3. User - SANS le champ 'name'
            $user = User::updateOrCreate(
                ['personnel_id' => $personnel->id],
                [
                    'email'                     => $userData['email'],
                    'password'                  => $userData['password'],
                    'status'                    => $userData['status'],
                    'email_verified_at'         => Carbon::now(),
                    'is_signer'                 => $userData['is_signer'],
                    'signataire_poste'          => $userData['is_signer'] ? $userData['poste'] : null,
                    'signataire_sigle'          => $userData['is_signer'] ? ($userData['poste'] === 'Directeur Général' ? 'DG' : ($userData['poste'] === 'Directeur Technique' ? 'DT' : 'DTA')) : null,
                    'signataire_ordre'          => $userData['is_signer'] ? ($userData['poste'] === 'Directeur Général' ? 1 : ($userData['poste'] === 'Directeur Technique' ? 2 : 3)) : null,
                    'signataire_peut_par_ordre' => $userData['is_signer'] && $userData['poste'] !== 'Directeur Général',
                ]
>>>>>>> a3f3c4d71fcca141b9bc9600e2b9c87382976f8f
            );

            // 4. Rôles
            $presetService->assignRolesAndPermissions(
                $user,
                [$userData['role']],
                $presetService->permissionsForRoles([$userData['role']])
            );

            // 5. Permission signer_attestation
            if ($userData['is_signer']) {
                $user->givePermissionTo('signer_attestation');
                $this->command->info("✓ Signataire : {$userData['prenom']} {$userData['nom']} ({$userData['poste']})");
            } else {
                $this->command->info("✓ Admin : {$userData['prenom']} {$userData['nom']}");
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->command->info('UserSeeder terminé.');
    }
}