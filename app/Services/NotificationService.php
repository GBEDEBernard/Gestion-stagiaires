<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Etudiant;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Générer les notifications automatiquement
     */
    public function generateNotifications()
    {
        $userId = Auth::id();

        // 1. Nouveaux étudiants inscrits ces 7 derniers jours
        $nouveauxEtudiants = Etudiant::where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($nouveauxEtudiants as $etudiant) {
            $this->createNotificationIfNotExists(
                'etudiant_' . $etudiant->id,
                $userId,
                'nouveau',
                'Nouvel étudiant',
                $etudiant->nom . ' ' . $etudiant->prenom . ' s\'est inscrit',
                'user-plus',
                'blue',
                route('etudiants.edit', $etudiant->id),
                $etudiant->id,
                Etudiant::class,
                $etudiant->created_at
            );
        }

        // 2. Stages qui finissent dans moins d'une semaine
        $stagesFinSemaine = Stage::where('date_fin', '>=', now())
            ->where('date_fin', '<=', now()->addDays(7))
            ->where('date_fin', '>=', now())
            ->with('etudiant')
            ->orderBy('date_fin', 'asc')
            ->get();

        foreach ($stagesFinSemaine as $stage) {
            $joursRestants = now()->diffInDays($stage->date_fin, false);
            $this->createNotificationIfNotExists(
                'stage_fin_' . $stage->id,
                $userId,
                'stage_fin_semaine',
                'Stage bientôt terminé',
                $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' - Fin dans ' . $joursRestants . ' jour(s)',
                'clock',
                'amber',
                encrypted_route('stages.show', $stage),
                $stage->id,
                Stage::class,
                $stage->date_fin
            );
        }

        // 3. Stages récemment terminés (ces 7 derniers jours)
        $stagesTermines = Stage::where('date_fin', '<', now())
            ->where('date_fin', '>=', now()->subDays(7))
            ->with('etudiant')
            ->orderBy('date_fin', 'desc')
            ->get();

        foreach ($stagesTermines as $stage) {
            $this->createNotificationIfNotExists(
                'stage_termine_' . $stage->id,
                $userId,
                'stage_termine',
                'Stage terminé',
                $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' a terminé son stage',
                'check-circle',
                'green',
                encrypted_route('stages.show', $stage),
                $stage->id,
                Stage::class,
                $stage->date_fin
            );
        }
    }

    /**
     * Créer une notification si elle n'existe pas déjà
     */
    private function createNotificationIfNotExists($uniqueId, $userId, $type, $title, $message, $icon, $color, $url, $referenceId = null, $referenceType = null, $time = null)
    {
        $exists = AppNotification::where('unique_id', $uniqueId)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            AppNotification::create([
                'unique_id' => $uniqueId,
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'icon' => $icon,
                'color' => $color,
                'url' => $url,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'created_at' => $time ?? now(),
            ]);
        }
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($notificationId)
    {
        $notification = AppNotification::find($notificationId);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
        }
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Obtenir les notifications non lues
     */
    public function getUnreadNotifications()
    {
        return AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir le nombre de notifications non lues
     */
    public function getUnreadCount()
    {
        return AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }
}
