<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'GBEDE Bernard',
                'email' => 'gbedebernard60@gmail.com',
                'password' => Hash::make('aqwzsxedc'),
                'status' => 'actif',
                'role' => 'admin',
            ],
            [
                'name' => 'Utilisateur Test1',
                'email' => 'gbedebernard61@gmail.com',
                'password' => Hash::make('aqwzsxedc'),
                'status' => 'actif',
                'role' => 'user',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'status' => $userData['status'],
                ]
            );

            // Assigner le rôle via Spatie
            $user->assignRole($userData['role']);
        }
    }
}
