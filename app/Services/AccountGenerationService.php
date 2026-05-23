<?php
// app/Services/AccountGenerationService.php
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

        // S'assurer que la relation personnable est chargée pour pouvoir
        // récupérer domaine_id si c'est un employé.
        $personnel->loadMissing('personnable');

        // Récupérer le domaine_id depuis la fiche Employe si elle existe
        $domaineId = null;
        if ($personnel->isEmploye() && $personnel->employe) {
            $domaineId = $personnel->employe->domaine_id;
        }

        $tempPassword = $customPassword ?? Str::random(10);

        $user = User::create([
            'personnel_id'                  => $personnel->id,
            'email'                         => $personnel->email,
            'domaine_id'                    => $domaineId,
            'password'                      => Hash::make($tempPassword),
            'must_change_password'          => true,
            'temporary_password_created_at' => now(),
            'status'                        => 'actif',
        ]);

        $user->assignRole($roleName);

        // Générer le token de réinitialisation et notifier l'utilisateur
        $token = Password::broker()->createToken($user);
        $user->notify(new AccountProvisionedNotification($token, $personnel->email));

        // Journalisation
        try {
            Log::info('account.generated', [
                'personnel_id' => $personnel->id,
                'user_id'      => $user->id,
                'role'         => $roleName,
                'generated_by' => auth()->id(),
                'timestamp'    => now()->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log account generation: ' . $e->getMessage());
        }

        return $user;
    }
}
