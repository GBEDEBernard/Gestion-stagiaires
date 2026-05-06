<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class EtudiantAccountService
{
    public function __construct(
        protected RolePermissionPresetService $rolePermissionPresetService
    ) {
    }

    public function syncMany(iterable $etudiants): array
    {
        // jb -> Cette synchro en masse sert au rattrapage:
        // utile quand des fiches etudiants existent deja avant la mise
        // en place du nouveau parcours de compte.
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
        // jb -> Methode pivot du lien etudiant <-> user.
        // Elle couvre les 3 cas metier:
        // 1. compte deja lie
        // 2. compte existant a rattacher
        // 3. nouveau compte a creer
        $linkedUser = $etudiant->user;

        if ($linkedUser) {
            $emailChanged = $linkedUser->email !== $etudiant->email;

            // jb -> Si l'email change cote etudiant, on resynchronise
            // le compte et on force une nouvelle verification de l'adresse.
            $linkedUser->update([
                'name' => $this->buildDisplayName($etudiant),
                'email' => $etudiant->email,
                'status' => 'actif',
                'email_verified_at' => $emailChanged ? null : $linkedUser->email_verified_at,
            ]);

            $this->rolePermissionPresetService->ensureRoleDefaults($linkedUser, ['etudiant']);

            return [
                'user' => $linkedUser->fresh(),
                'temporary_password' => null,
                'created' => false,
                'verification_email_sent' => $emailChanged ? $this->sendVerificationEmailSafely($linkedUser) : false,
            ];
        }

        $existingUser = User::where('email', $etudiant->email)->first();

        if ($existingUser) {
            // jb -> On refuse de recycler un compte deja rattache a un autre
            // etudiant ou a un compte privilegie pour eviter toute confusion
            // de responsabilite ou de securite.
            if ($existingUser->etudiant && (int) $existingUser->etudiant->id !== (int) $etudiant->id) {
                throw ValidationException::withMessages([
                    'email' => "Cet email est deja rattache a un autre etudiant.",
                ]);
            }

            if ($existingUser->hasAnyRole(['admin', 'superviseur'])) {
                throw ValidationException::withMessages([
                    'email' => "Cet email appartient deja a un compte privilegie. Utilise un email dedie pour l'etudiant.",
                ]);
            }

            $existingUser->update([
                'name' => $this->buildDisplayName($etudiant),
                'status' => 'actif',
            ]);
            $this->rolePermissionPresetService->ensureRoleDefaults($existingUser, ['etudiant']);

            $etudiant->user()->associate($existingUser);
            $etudiant->save();

            return [
                'user' => $existingUser->fresh(),
                'temporary_password' => null,
                'created' => false,
                'verification_email_sent' => false,
            ];
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $newUser = User::create([
            'name' => $this->buildDisplayName($etudiant),
            'email' => $etudiant->email,
            'password' => $temporaryPassword,
            'status' => 'actif',
            'email_verified_at' => null,
            'must_change_password' => true,
            'temporary_password_created_at' => now(),
            'password_changed_at' => null,
        ]);
        $this->rolePermissionPresetService->ensureRoleDefaults($newUser, ['etudiant']);

        // jb -> Le stagiaire doit etre rattache tout de suite a sa fiche,
        // mais son compte reste sous controle tant que l'email et le mot
        // de passe personnel ne sont pas finalises.
        $etudiant->user()->associate($newUser);
        $etudiant->save();

        return [
            'user' => $newUser,
            'temporary_password' => $temporaryPassword,
            'created' => true,
            'verification_email_sent' => $this->sendVerificationEmailSafely($newUser),
        ];
    }

    public function linkExistingUserToEtudiant(User $user): void
    {
        // jb -> Ce lien silencieux permet a un admin de creer d'abord
        // le compte, puis de laisser le systeme raccrocher la fiche
        // etudiant des que l'email correspond.
        if ($user->etudiant) {
            return;
        }

        $etudiant = Etudiant::where('email', $user->email)->first();

        if (!$etudiant) {
            return;
        }

        if ($etudiant->user_id && (int) $etudiant->user_id !== (int) $user->id) {
            throw ValidationException::withMessages([
                'email' => "Une autre fiche etudiant est deja rattachee a cet email.",
            ]);
        }

        $etudiant->user()->associate($user);
        $etudiant->save();
    }

    protected function buildDisplayName(Etudiant $etudiant): string
    {
        // jb -> On centralise le nom d'affichage pour garder la meme
        // convention partout entre la fiche etudiante et le compte user.
        return trim($etudiant->prenom . ' ' . $etudiant->nom);
    }

    protected function generateTemporaryPassword(): string
    {
        return 'Jb-' . Str::upper(Str::random(4)) . '!' . random_int(1000, 9999);
    }

    protected function sendVerificationEmailSafely(User $user): bool
    {
        // jb -> Meme principe que cote admin:
        // un souci SMTP ne doit pas bloquer la creation ou la synchro
        // de la fiche etudiante.
        try {
            $user->sendEmailVerificationNotification();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
