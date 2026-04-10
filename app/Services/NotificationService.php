<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Etudiant;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Generer les notifications automatiquement.
     */
    public function generateNotifications()
    {
        $userId = Auth::id();
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

        $nouveauxEtudiants = Etudiant::where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($nouveauxEtudiants as $etudiant) {
            $this->createNotificationIfNotExists(
                'etudiant_' . $etudiant->id,
                $userId,
                'nouveau',
                'Nouvel etudiant',
                $etudiant->nom . ' ' . $etudiant->prenom . ' s\'est inscrit',
                'user-plus',
                'blue',
                route('etudiants.edit', $etudiant->id),
                $etudiant->id,
                Etudiant::class,
                $etudiant->created_at
            );
        }

        $stagesFinSemaine = Stage::where('date_fin', '>=', now())
            ->where('date_fin', '<=', now()->addDays(7))
            ->with('etudiant')
            ->orderBy('date_fin', 'asc')
            ->get();

        foreach ($stagesFinSemaine as $stage) {
            $joursRestants = now()->diffInDays($stage->date_fin, false);

            $this->createNotificationIfNotExists(
                'stage_fin_' . $stage->id,
                $userId,
                'stage_fin_semaine',
                'Stage bientot termine',
                $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' - Fin dans ' . $joursRestants . ' jour(s)',
                'clock',
                'amber',
                encrypted_route('stages.show', $stage),
                $stage->id,
                Stage::class,
                $stage->date_fin
            );
        }

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
                'Stage termine',
                $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' a termine son stage',
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
     */
    public function markAsRead($notificationId)
    {
        $notification = AppNotification::find($notificationId);

        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
        }
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Obtenir les notifications non lues.
     */
    public function getUnreadNotifications()
    {
        return AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir le nombre de notifications non lues.
     */
    public function getUnreadCount()
    {
        return AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }
}
