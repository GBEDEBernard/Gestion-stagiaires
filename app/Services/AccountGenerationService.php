<?php

namespace App\Services;

use App\Models\Personnel;
use App\Models\User;
use App\Notifications\AccountProvisionedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountGenerationService
{
    public function generateForPersonnel(Personnel $personnel, string $roleName): User
    {
        if ($personnel->user) {
            throw new \Exception('Un compte existe déjà pour ce personnel.');
        }

        $tempPassword = Str::random(10); // ou mieux : Str::random(8) . rand(100,999)
        $user = User::create([
            'personnel_id' => $personnel->id,
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
            'temporary_password_created_at' => now(),
            'status' => 'actif',
        ]);

        $user->assignRole($roleName);
        $user->notify(new AccountProvisionedNotification($tempPassword));

        return $user;
    }
}