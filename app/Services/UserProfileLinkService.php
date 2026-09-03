<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserProfileLinkService
{
    public function ensureStudentProfile(User $user): ?Etudiant
    {
        if (!$user->hasRole('etudiant')) {
            return null;
        }

        if ($user->etudiant) {
            return $user->etudiant;
        }

        return DB::transaction(function () use ($user) {
            $user->refresh();

            if ($user->etudiant) {
                return $user->etudiant;
            }

            $personnel = $this->resolvePersonnel($user);

            if ($personnel->personnable_type === Etudiant::class && $personnel->personnable_id) {
                $etudiant = Etudiant::find($personnel->personnable_id);

                if ($etudiant) {
                    if (!$etudiant->personnel_id) {
                        $etudiant->forceFill(['personnel_id' => $personnel->id])->save();
                    }

                    return $etudiant;
                }
            }

            $etudiant = Etudiant::firstOrCreate(
                ['personnel_id' => $personnel->id],
                ['ecole' => null]
            );

            $personnel->forceFill([
                'personnable_type' => Etudiant::class,
                'personnable_id' => $etudiant->id,
            ])->save();

            return $etudiant;
        });
    }

    private function resolvePersonnel(User $user): Personnel
    {
        if ($user->personnel) {
            return $user->personnel;
        }

        $email = $user->getRawOriginal('email') ?: $user->email ?: "user-{$user->id}@local.invalid";
        $personnel = Personnel::where('email', $email)->first();

        if ($personnel && (!$personnel->user || $personnel->user->is($user))) {
            $user->forceFill(['personnel_id' => $personnel->id])->save();

            return $personnel;
        }

        [$prenom, $nom] = $this->splitDisplayName($user);

        $personnel = Personnel::create([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $personnel ? "user-{$user->id}@local.invalid" : $email,
        ]);

        $user->forceFill(['personnel_id' => $personnel->id])->save();

        return $personnel;
    }

    private function splitDisplayName(User $user): array
    {
        $name = trim((string) ($user->getRawOriginal('name') ?: $user->name ?: ''));

        if ($name === '') {
            return ['Utilisateur', "#{$user->id}"];
        }

        $parts = preg_split('/\s+/', $name, 2);

        return [
            $parts[0] ?: 'Utilisateur',
            $parts[1] ?? "#{$user->id}",
        ];
    }
}
