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
use Illuminate\Support\Facades\Schema;
use Throwable;

class AccountGenerationService
{
    private bool $lastProvisioningEmailSent = false;

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

        $tempPassword = $this->normalizePassword($customPassword) ?? Str::random(10);

        $userData = [
            'personnel_id'                  => $personnel->id,
            'email'                         => $personnel->email,
            'domaine_id'                    => $domaineId,
            'password'                      => Hash::make($tempPassword),
            'must_change_password'          => true,
            'temporary_password_created_at' => now(),
            'status'                        => 'actif',
        ];

        if (Schema::hasColumn('users', 'name')) {
            $userData['name'] = $personnel->full_name;
        }

        $user = User::create($userData);

        $user->assignRole($roleName);

        $this->lastProvisioningEmailSent = $this->sendProvisioningEmail($user, $personnel);

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

    public function lastProvisioningEmailSent(): bool
    {
        return $this->lastProvisioningEmailSent;
    }

    public function resendProvisioningEmail(Personnel $personnel): User
    {
        $user = $personnel->user;

        if (!$user) {
            throw new \Exception('Aucun compte utilisateur n\'est lié à ce personnel.');
        }

        $this->syncAccountIdentity($user, $personnel);
        $this->lastProvisioningEmailSent = $this->sendProvisioningEmail($user, $personnel);

        try {
            Log::info('account.provisioning_email_resent', [
                'personnel_id' => $personnel->id,
                'user_id'      => $user->id,
                'resent_by'    => auth()->id(),
                'timestamp'    => now()->toDateTimeString(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log account provisioning email resend: ' . $e->getMessage());
        }

        return $user->fresh();
    }

    private function sendProvisioningEmail(User $user, Personnel $personnel): bool
    {
        try {
            $token = Password::broker()->createToken($user);

            $user->notify(new AccountProvisionedNotification($token, $personnel->email));

            return true;
        } catch (Throwable $e) {
            Log::error('account.provisioning_email_failed', [
                'personnel_id' => $personnel->id,
                'user_id' => $user->id,
                'email' => $personnel->email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function syncAccountIdentity(User $user, Personnel $personnel): void
    {
        $userData = [
            'personnel_id' => $personnel->id,
            'email'        => $personnel->email,
            'status'       => 'actif',
        ];

        if ($personnel->isEmploye() && $personnel->employe) {
            $userData['domaine_id'] = $personnel->employe->domaine_id;
        }

        if (Schema::hasColumn('users', 'name')) {
            $userData['name'] = $personnel->full_name;
        }

        $user->forceFill($userData)->save();
    }

    private function normalizePassword(?string $password): ?string
    {
        $password = is_string($password) ? trim($password) : $password;

        return $password === '' ? null : $password;
    }
}
