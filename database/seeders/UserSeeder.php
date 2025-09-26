<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'GBEDE Bernard',
                'email' => 'gbedebernard60@gmail.com',
                'password' => Hash::make('aqwzsxedc') // mot de passe
            ],
            [
                'name' => 'Utilisateur Test1',
                'email' => 'gbedebernard61@gmail.com',
                'password' => Hash::make('aqwzsxedc')
            ],
            [
                'name' => 'Utilisateur Test 2',
                'email' => 'user2@example.com',
                'password' => Hash::make('password123')
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // évite les doublons
                ['name' => $user['name'], 'password' => $user['password']]
            );
        }
    }
}
