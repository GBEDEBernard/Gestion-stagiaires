<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
<<<<<<< HEAD
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;
=======
use App\Services\RolePermissionPresetService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
>>>>>>> e9635ab

class UserSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
=======
        $presetService = app(RolePermissionPresetService::class);

>>>>>>> e9635ab
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
<<<<<<< HEAD
                'role' => 'user',
=======
                'role' => 'etudiant',
            ],
            [
                'name' => 'Superviseur Test',
                'email' => 'superviseur@gst.local',
                'password' => Hash::make('Superviseur123!'),
                'status' => 'actif',
                'role' => 'superviseur',
>>>>>>> e9635ab
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'status' => $userData['status'],
                    'email_verified_at' => Carbon::now(), // <-- ajoute ça
                ]
            );

            // Assigner le rôle via Spatie
<<<<<<< HEAD
            $user->assignRole($userData['role']);
=======
            $presetService->assignRolesAndPermissions(
                $user,
                [$userData['role']],
                $presetService->permissionsForRoles([$userData['role']])
            );
>>>>>>> e9635ab
        }
    }
}
