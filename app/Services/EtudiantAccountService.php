<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class EtudiantAccountService
{
    public function __construct(
        protected RolePermissionPresetService $rolePermissionPresetService
    ) {}

    public function syncMany(iterable $etudiants): array
    {
        $results = [];

        foreach ($etudiants as $etudiant) {
            $results[] = [
                'etudiant_id' => $etudiant->id,
                'etudiant_name' => $this->buildDisplayName($etudiant),
                ...$this->ensureLinkedUser($etudiant),
            ];
        }

        return $results;
    }

    public function ensureLinkedUser(Etudiant $etudiant): array
    {
        $personnel = $this->ensurePersonnelForEtudiant($etudiant);
        $email = $personnel->email;
        $linkedUser = $etudiant->user;

        if ($linkedUser) {
            $emailChanged = $linkedUser->getRawOriginal('email') !== $email;

            $userData = [
                'email' => $email,
                'status' => 'actif',
                'email_verified_at' => $emailChanged ? null : $linkedUser->email_verified_at,
                'personnel_id' => $personnel->id,
            ];

            if (Schema::hasColumn('users', 'name')) {
                $userData['name'] = $personnel->full_name;
            }

            $linkedUser->update($userData);

            $this->rolePermissionPresetService->ensureRoleDefaults($linkedUser, ['etudiant']);

            return [
                'user' => $linkedUser->fresh(),
                'temporary_password' => null,
                'created' => false,
                'verification_email_sent' => $emailChanged ? $this->sendVerificationEmailSafely($linkedUser) : false,
            ];
        }

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if ($existingUser->personnel_id && (int) $existingUser->personnel_id !== (int) $personnel->id) {
                throw ValidationException::withMessages([
                    'email' => "Cet email est deja rattache a un autre personnel.",
                ]);
            }

            if ($existingUser->hasAnyRole(['admin', 'superviseur'])) {
                throw ValidationException::withMessages([
                    'email' => "Cet email appartient deja a un compte privilegie. Utilise un email dedie pour l'etudiant.",
                ]);
            }

            $userData = [
                'status' => 'actif',
                'personnel_id' => $personnel->id,
            ];

            if (Schema::hasColumn('users', 'name')) {
                $userData['name'] = $personnel->full_name;
            }

            $existingUser->update($userData);

            $this->rolePermissionPresetService->ensureRoleDefaults($existingUser, ['etudiant']);

            return [
                'user' => $existingUser->fresh(),
                'temporary_password' => null,
                'created' => false,
                'verification_email_sent' => false,
            ];
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $userData = [
            'personnel_id' => $personnel->id,
            'email' => $email,
            'password' => Hash::make($temporaryPassword),
            'status' => 'actif',
            'email_verified_at' => null,
            'must_change_password' => true,
            'temporary_password_created_at' => now(),
            'password_changed_at' => null,
        ];

        if (Schema::hasColumn('users', 'name')) {
            $userData['name'] = $personnel->full_name;
        }

        $newUser = User::create($userData);

        $this->rolePermissionPresetService->ensureRoleDefaults($newUser, ['etudiant']);

        return [
            'user' => $newUser,
            'temporary_password' => $temporaryPassword,
            'created' => true,
            'verification_email_sent' => $this->sendVerificationEmailSafely($newUser),
        ];
    }

    public function linkExistingUserToEtudiant(User $user): void
    {
        if ($user->etudiant) {
            return;
        }

        $etudiant = Etudiant::whereHas('personnel', function ($query) use ($user) {
            $query->where('email', $user->getRawOriginal('email'));
        })->first();

        if (!$etudiant || !$etudiant->personnel) {
            return;
        }

        if ($user->personnel_id && (int) $user->personnel_id !== (int) $etudiant->personnel_id) {
            throw ValidationException::withMessages([
                'email' => "Une autre fiche personnel est deja rattachee a ce compte.",
            ]);
        }

        $user->update(['personnel_id' => $etudiant->personnel_id]);
    }

    protected function ensurePersonnelForEtudiant(Etudiant $etudiant): Personnel
    {
        if ($etudiant->personnel) {
            return $etudiant->personnel;
        }

        throw ValidationException::withMessages([
            'personnel' => "Cette fiche etudiant n'est pas rattachee a une fiche personnel.",
        ]);
    }

    protected function buildDisplayName(Etudiant $etudiant): string
    {
        return $etudiant->personnel?->full_name ?? 'Etudiant';
    }

    protected function generateTemporaryPassword(): string
    {
        return 'Jb-' . Str::upper(Str::random(4)) . '!' . random_int(1000, 9999);
    }

    protected function sendVerificationEmailSafely(User $user): bool
    {
        try {
            $user->sendEmailVerificationNotification();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
