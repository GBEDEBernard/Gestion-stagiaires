<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Désactive automatiquement les comptes des stagiaires dont tous les stages
 * sont terminés (date_fin < aujourd'hui). Un étudiant sans stage n'est pas
 * concerné, ni un étudiant avec un stage en cours ou à venir.
 */
class StudentStageExpiryService
{
    /**
     * Désactive les comptes des étudiants dont tous les stages sont terminés.
     * Retourne le nombre de comptes désactivés.
     */
    public function deactivateExpiredAccounts(): int
    {
        $count = 0;

        User::role('etudiant')
            ->where('status', 'actif')
            ->with('etudiant')
            ->chunkById(100, function ($users) use (&$count) {
                foreach ($users as $user) {
                    if (!$this->hasExpiredStage($user)) {
                        continue;
                    }

                    $user->forceFill(['status' => 'inactif'])->save();
                    $count++;
                }
            });

        return $count;
    }

    /**
     * Un étudiant a-t-il au moins un stage et tous ses stages terminés ?
     */
    public function hasExpiredStage(User $user): bool
    {
        $etudiant = $user->etudiant;

        if (!$etudiant) {
            return false;
        }

        $totalStages = $etudiant->stages()->count();

        if ($totalStages === 0) {
            return false;
        }

        $hasActiveOrUpcomingStage = $etudiant->stages()
            ->where('date_fin', '>=', today()->startOfDay())
            ->exists();

        return !$hasActiveOrUpcomingStage;
    }
}