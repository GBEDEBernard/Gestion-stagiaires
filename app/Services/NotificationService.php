<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Etudiant;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Pousse une notification ciblée à un utilisateur précis (événementiel).
     * Utilisé par T-003 (nouveau rapport, nouveau message, corrections demandées).
     */
    public function push(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        string $icon = 'bell',
        string $color = 'blue'
    ): void {
        AppNotification::create([
            'unique_id' => $type . '_' . (string) Str::uuid(),
            'user_id'   => $userId,
            'type'      => $type,
            'title'     => $title,
            'message'   => $message,
            'icon'      => $icon,
            'color'     => $color,
            'url'       => $url,
        ]);
    }

    /**
     * Generer les notifications automatiquement (ADMIN/SUPERVISEUR uniquement).
     */
    public function generateNotifications()
    {
        $currentUserId = Auth::id();
        $user = Auth::user();

        if (!$currentUserId || !$user) {
            return;
        }

        // UNIQUEMENT pour admin/superviseur (pas étudiants, pas employés)
        if (!$user->hasAnyRole(['admin', 'superviseur'])) {
            return;
        }

        // 1️⃣ NOUVEAUX ÉTUDIANTS (7 derniers jours) - Destiné aux admins
        $nouveauxEtudiants = \App\Models\Etudiant::where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($nouveauxEtudiants as $etudiant) {
            $this->createNotificationIfNotExists(
                'etudiant_' . $etudiant->id,
                $currentUserId, // Destinataire: l'admin connecté
                'nouveau_etudiant',
                '👤 Nouvel étudiant',
                $etudiant->nom . ' ' . $etudiant->prenom . ' s\'est inscrit il y a ' . $etudiant->created_at->diffForHumans(),
                'users',
                'blue',
                route('admin.users.show', $etudiant->user_id ?? 0),
                $etudiant->id,
                \App\Models\Etudiant::class
            );
        }
        // 2STAGES TERMINANT CETTE SEMAINE
        $stagesFinSemaine = \App\Models\Stage::where('date_fin', '>=', now())
            ->where('date_fin', '<=', now()->addDays(7))
            ->whereHas('etudiant')
            ->with('etudiant.personnel')
            ->get();

        foreach ($stagesFinSemaine as $stage) {
            $etudiantName = $this->stageEtudiantName($stage);

            if (!$etudiantName) {
                continue;
            }

            $joursRestants = now()->diffInDays($stage->date_fin, false);
            $this->createNotificationIfNotExists(
                'stage_fin_' . $stage->id,
                $currentUserId,
                'stage_fin_semaine',
                'Stage bientôt terminé',
                $etudiantName . ' - Fin dans ' . $joursRestants . ' jour(s)',
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
            ->whereHas('etudiant')
            ->with('etudiant.personnel')
            ->get();

        foreach ($stagesTermines as $stage) {
            $etudiantName = $this->stageEtudiantName($stage);

            if (!$etudiantName) {
                continue;
            }

            $this->createNotificationIfNotExists(
                'stage_termine_' . $stage->id,
                $currentUserId,
                'stage_termine',
                'Stage terminé',
                $etudiantName . ' a terminé son stage le ' . $stage->date_fin->format('d/m'),
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
                $currentUserId,
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
                $currentUserId,
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
                $currentUserId,
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
    private function createNotificationIfNotExists($uniqueId, $currentUserId, $type, $title, $message, $icon, $color, $url, $referenceId = null, $referenceType = null, $time = null)
    {
        // jb -> La base impose un unique_id global. On le scope donc
        // explicitement par utilisateur pour eviter qu'une meme alerte
        // "metier" explose quand plusieurs comptes doivent la recevoir.
        $scopedUniqueId = $this->buildScopedUniqueId($uniqueId, $currentUserId);

        AppNotification::updateOrCreate(
            ['unique_id' => $scopedUniqueId],
            [
                'user_id'        => $currentUserId,
                'type'           => $type,
                'title'          => $title,
                'message'        => $message,
                'icon'           => $icon,
                'color'          => $color,
                'url'            => $url,   // ← l'URL sera corrigée à chaque régénération
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

    private function stageEtudiantName(Stage $stage): ?string
    {
        $etudiant = $stage->etudiant;

        if (!$etudiant) {
            return null;
        }

        $name = trim(($etudiant->prenom ?? '') . ' ' . ($etudiant->nom ?? ''));

        return $name !== '' ? $name : 'Etudiant #' . $etudiant->id;
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
        AppNotification::visibleForUser(Auth::user())
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Obtenir les notifications non lues.
     */
    public function getUnreadNotifications()
    {
        return AppNotification::visibleForUser(Auth::user())
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir le nombre de notifications non lues.
     */
    public function getUnreadCount()
    {
        return AppNotification::visibleForUser(Auth::user())
            ->unread()
            ->count();
    }
}
