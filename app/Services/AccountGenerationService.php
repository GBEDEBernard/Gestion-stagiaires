<?php

namespace App\Services;

use App\Models\Personnel;
use App\Models\User;
use App\Notifications\AccountProvisionedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class AccountGenerationService
{
    public function generateForPersonnel(Personnel $personnel, string $roleName, ?string $customPassword = null): User
    {
        if ($personnel->user) {
            throw new \Exception('Un compte existe déjà pour ce personnel.');
        }

        $tempPassword = $customPassword ?? Str::random(10); // Use custom password if provided
        $user = User::create([
            'personnel_id' => $personnel->id,
            'name'         => $personnel->full_name,       // nom complet
            'email'        => $personnel->email,          // email unique
            'domaine_id'   => $personnel->employe ? $personnel->employe->domaine_id : null, // Associer domaine si c'est un employé
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
            'temporary_password_created_at' => now(),
            'status' => 'actif',
        ]);


        $user->assignRole($roleName);

        // Générer un token de réinitialisation
        $token = Password::broker()->createToken($user);

        // Notifier l'utilisateur avec le token (et l'email) pour construire une URL standard
        $user->notify(new AccountProvisionedNotification($token, $personnel->email));


        // Audit simple : enregistrer qui a généré le compte dans les logs
        try {
            $actorId = auth()->id() ?? null;
            Log::info('account.generated', [
                'personnel_id' => $personnel->id,
                'user_id' => $user->id,
                'role' => $roleName,
                'generated_by' => $actorId,
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            // ne pas faire échouer la création de compte pour un échec de logging
            Log::error('Failed to log account generation: ' . $e->getMessage());
        }

        return $user;
    }
}
