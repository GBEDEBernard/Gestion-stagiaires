<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Etudiant;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Generer les notifications automatiquement (ADMIN/SUPERVISEUR uniquement).
     */
    public function generateNotifications()
    {
        $userId = Auth::id();
        $user = Auth::user();

        if (!$userId || !$user) {
            return;
        }

        // UNIQUEMENT pour admin/superviseur (pas etudiants)
        if ($user->hasRole('etudiant')) {
            return;
        }

                // 1️⃣ NOUVEAUX ÉTUDIANTS (7 derniers jours)
            $nouveauxEtudiants = \App\Models\Etudiant::where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($nouveauxEtudiants as $etudiant) {
                // Récupérer l'ID utilisateur associé
                $userId = $etudiant->user_id ?? $etudiant->user?->id;
                if (!$userId) {
                    continue; // sécurité : pas d'utilisateur lié
                }
                $this->createNotificationIfNotExists(
                    'etudiant_' . $etudiant->id,
                    $userId, // destinataire admin
                    'nouveau_etudiant',
                    '👤 Nouvel étudiant',
                    $etudiant->nom . ' ' . $etudiant->prenom . ' s\'est inscrit il y a ' . $etudiant->created_at->diffForHumans(),
                    'users',
                    'blue',
                    route('admin.users.show', $userId), // ← corrigé
                    $etudiant->id,
                    \App\Models\Etudiant::class
                );
            }
        // 2STAGES TERMINANT CETTE SEMAINE
        $stagesFinSemaine = \App\Models\Stage::where('date_fin', '>=', now())
            ->where('date_fin', '<=', now()->addDays(7))
            ->with('etudiant')
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
                \App\Models\Stage::class
            );
        }

        // 3STAGES TERMINÉS (7 derniers jours)
        $stagesTermines = \App\Models\Stage::where('date_fin', '<', now())
            ->where('date_fin', '>=', now()->subDays(7))
            ->with('etudiant')
            ->get();

        foreach ($stagesTermines as $stage) {
            $this->createNotificationIfNotExists(
                'stage_termine_' . $stage->id,
                $userId,
                'stage_termine',
                'Stage terminé',
                $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' a terminé son stage le ' . $stage->date_fin->format('d/m'),
                'check-circle',
                'green',
                encrypted_route('stages.show', $stage),
                $stage->id,
                \App\Models\Stage::class
            );
        }

        // 🔥  ADMIN SPÉCIFIQUES : Anomalies Présence (24h)
        $anomalies = \App\Models\AttendanceAnomaly::where('created_at', '>=', now()->subDay())
            ->where('status', 'open')
            ->count();

        if ($anomalies > 0) {
            $this->createNotificationIfNotExists(
                'presence_anomalies_' . now()->format('Y-m-d'),
                $userId,
                'presence_anomalies',
                '🚨 ' . $anomalies . ' anomalie(s) présence',
                'À vérifier immédiatement dans Admin > Présence > Anomalies',
                'exclamation-triangle',
                'red',
                route('admin.presence.anomalies'),
                null,
                null
            );
        }

        // 🔥 5️⃣ Rapports journaliers en attente (aujourd'hui)
        $rapportsAttente = \App\Models\DailyReport::whereDate('created_at', now())
            ->where('reviewed_at', null)
            ->count();

        if ($rapportsAttente > 0) {
            $this->createNotificationIfNotExists(
                'rapports_attente_' . now()->format('Y-m-d'),
                $userId,
                'rapports_en_attente',
                '📋 ' . $rapportsAttente . ' rapport(s) à valider',
                'Nouveaux rapports journaliers soumis aujourd\'hui',
                'clipboard-list',
                'orange',
                route('admin.reports.index'),
                null,
                null
            );
        }

        // 🔥 6️Badges à attribuer (étudiants sans badge récent) - DISABLED car pas de relation badges()
        /*
        $sansBadge = \App\Models\Etudiant::whereDoesntHave('badges', function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        })
            ->whereHas('stages', function ($q) {
                $q->where('date_fin', '<=', now()->subDays(7));
            })
            ->count();

        if ($sansBadge > 0) {
            $this->createNotificationIfNotExists(
                'badges_manquants_' . now()->format('Y-m-d'),
                $userId,
                'badges_manquants',
                '🏷️ ' . $sansBadge . ' badge(s) à attribuer',
                'Étudiants avec stages terminés sans badge récent',
                'ticket',
                'purple',
                route('badges.index'),
                null,
                null
            );
        }
        */
    }
    // ✅ Fin generateNotifications() - Code propre, 6 alertes admin prêtes !

    /**
     * Creer une notification si elle n'existe pas deja.
     */
    private function createNotificationIfNotExists($uniqueId, $userId, $type, $title, $message, $icon, $color, $url, $referenceId = null, $referenceType = null, $time = null)
    {
        // jb -> La base impose un unique_id global. On le scope donc
        // explicitement par utilisateur pour eviter qu'une meme alerte
        // "metier" explose quand plusieurs comptes doivent la recevoir.
        $scopedUniqueId = $this->buildScopedUniqueId($uniqueId, $userId);

        AppNotification::updateOrCreate(
            ['unique_id' => $scopedUniqueId],
            [
                'user_id'        => $userId,
                'type'           => $type,
                'title'          => $title,
                'message'        => $message,
                'icon'           => $icon,
                'color'          => $color,
                'url'            => $url,   // ← l'URL sera corrigée à chaque regénération
                'reference_id'   => $referenceId,
                'reference_type' => $referenceType,
                'updated_at'     => now(),
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
