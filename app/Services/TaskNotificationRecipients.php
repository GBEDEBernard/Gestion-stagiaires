<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class TaskNotificationRecipients
{
    /**
     * Récupère le superviseur direct de la tâche (propriétaire).
     */
    public function getSupervisor(Task $task): ?User
    {
        $owner = $task->owner;
        if (!$owner) return null;

        $profil = $owner->profil();
        if ($profil instanceof \App\Models\Employe) {
            return $profil->supervisor;
        }
        if ($profil instanceof \App\Models\Etudiant) {
            // D'abord le superviseur du stage actif
            $stage = $profil->stages()
                ->where('date_fin', '>=', now())
                ->orderBy('date_debut', 'desc')
                ->first();
            if ($stage && $stage->supervisor) {
                return $stage->supervisor;
            }
            // Sinon superviseur direct de l'étudiant
            return $profil->supervisor;
        }
        return null;
    }

    /**
     * Récupère tous les administrateurs.
     */
    public function getAdmins(): Collection
    {
        return User::role('admin')->get();
    }

    /**
     * Récupère tous les destinataires pour une tâche (superviseur + admins).
     */
    public function getAllRecipientsForTask(Task $task): Collection
    {
        $recipients = collect();
        $supervisor = $this->getSupervisor($task);
        if ($supervisor) {
            $recipients->push($supervisor);
        }
        return $recipients->merge($this->getAdmins())->unique('id');
    }
}