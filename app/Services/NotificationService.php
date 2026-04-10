<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Etudiant;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
<<<<<<< HEAD
     * Générer les notifications automatiquement
=======
     * Generer les notifications automatiquement.
>>>>>>> e9635ab
     */
    public function generateNotifications()
    {
        $userId = Auth::id();
<<<<<<< HEAD

        // 1. Nouveaux étudiants inscrits ces 7 derniers jours
=======
        $user = Auth::user();

        if (!$userId) {
            return;
        }

        // jb -> Les alertes generees ici servent au pilotage global
        // du back-office. Les comptes etudiants ne doivent donc pas
        // recevoir ces notifications d'administration generale.
        if ($user && $user->hasRole('etudiant')) {
            return;
        }

>>>>>>> e9635ab
        $nouveauxEtudiants = Etudiant::where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($nouveauxEtudiants as $etudiant) {
            $this->createNotificationIfNotExists(
                'etudiant_' . $etudiant->id,
                $userId,
                'nouveau',
<<<<<<< HEAD
                'Nouvel étudiant',
=======
                'Nouvel etudiant',
>>>>>>> e9635ab
                $etudiant->nom . ' ' . $etudiant->prenom . ' s\'est inscrit',
                'user-plus',
                'blue',
                route('etudiants.edit', $etudiant->id),
                $etudiant->id,
                Etudiant::class,
                $etudiant->created_at
            );
        }

<<<<<<< HEAD
        // 2. Stages qui finissent dans moins d'une semaine
        $stagesFinSemaine = Stage::where('date_fin', '>=', now())
            ->where('date_fin', '<=', now()->addDays(7))
            ->where('date_fin', '>=', now())
=======
        $stagesFinSemaine = Stage::where('date_fin', '>=', now())
            ->where('date_fin', '<=', now()->addDays(7))
>>>>>>> e9635ab
            ->with('etudiant')
            ->orderBy('date_fin', 'asc')
            ->get();

        foreach ($stagesFinSemaine as $stage) {
            $joursRestants = now()->diffInDays($stage->date_fin, false);
<<<<<<< HEAD
=======

>>>>>>> e9635ab
            $this->createNotificationIfNotExists(
                'stage_fin_' . $stage->id,
                $userId,
                'stage_fin_semaine',
<<<<<<< HEAD
                'Stage bientôt terminé',
=======
                'Stage bientot termine',
>>>>>>> e9635ab
                $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' - Fin dans ' . $joursRestants . ' jour(s)',
                'clock',
                'amber',
                encrypted_route('stages.show', $stage),
                $stage->id,
                Stage::class,
                $stage->date_fin
            );
        }

<<<<<<< HEAD
        // 3. Stages récemment terminés (ces 7 derniers jours)
=======
>>>>>>> e9635ab
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
<<<<<<< HEAD
                'Stage terminé',
                $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' a terminé son stage',
=======
                'Stage termine',
                $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' a termine son stage',
>>>>>>> e9635ab
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
<<<<<<< HEAD
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
=======
     * Creer une notification si elle n'existe pas deja.
     */
    private function createNotificationIfNotExists($uniqueId, $userId, $type, $title, $message, $icon, $color, $url, $referenceId = null, $referenceType = null, $time = null)
    {
        // jb -> La base impose un unique_id global. On le scope donc
        // explicitement par utilisateur pour eviter qu'une meme alerte
        // "metier" explose quand plusieurs comptes doivent la recevoir.
        $scopedUniqueId = $this->buildScopedUniqueId($uniqueId, $userId);

        AppNotification::firstOrCreate(
            ['unique_id' => $scopedUniqueId],
            [
>>>>>>> e9635ab
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
<<<<<<< HEAD
            ]);
        }
    }

    /**
     * Marquer une notification comme lue
=======
                'updated_at' => $time ?? now(),
            ]
        );
    }

    private function buildScopedUniqueId(string $uniqueId, int|string|null $userId): string
    {
        return "{$uniqueId}_user_{$userId}";
    }

    /**
     * Marquer une notification comme lue.
>>>>>>> e9635ab
     */
    public function markAsRead($notificationId)
    {
        $notification = AppNotification::find($notificationId);
<<<<<<< HEAD
=======

>>>>>>> e9635ab
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
        }
    }

    /**
<<<<<<< HEAD
     * Marquer toutes les notifications comme lues
=======
     * Marquer toutes les notifications comme lues.
>>>>>>> e9635ab
     */
    public function markAllAsRead()
    {
        AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
<<<<<<< HEAD
     * Obtenir les notifications non lues
=======
     * Obtenir les notifications non lues.
>>>>>>> e9635ab
     */
    public function getUnreadNotifications()
    {
        return AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
<<<<<<< HEAD
     * Obtenir le nombre de notifications non lues
=======
     * Obtenir le nombre de notifications non lues.
>>>>>>> e9635ab
     */
    public function getUnreadCount()
    {
        return AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }
}
