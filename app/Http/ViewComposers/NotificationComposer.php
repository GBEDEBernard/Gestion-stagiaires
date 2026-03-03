<?php

namespace App\Http\ViewComposers;

use App\Models\Etudiant;
use App\Models\Stage;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * Composer les données de notification pour toutes les vues
     */
    public function compose(View $view)
    {
        $notifications = $this->getNotifications();
        $unreadCount = $notifications->count();

        $view->with('notifications', $notifications);
        $view->with('notificationCount', $unreadCount);
    }

    /**
     * Récupérer toutes les notifications
     */
    private function getNotifications()
    {
        $notifications = collect();

        // 1. Nouveaux étudiants inscrits ces 7 derniers jours
        $nouveauxEtudiants = Etudiant::where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($nouveauxEtudiants as $etudiant) {
            $notifications->push([
                'id' => 'etudiant_' . $etudiant->id,
                'type' => 'nouveau',
                'title' => 'Nouvel étudiant',
                'message' => $etudiant->nom . ' ' . $etudiant->prenom . ' s\'est inscrit',
                'icon' => 'user-plus',
                'color' => 'blue',
                'time' => $etudiant->created_at,
                'url' => route('etudiants.edit', $etudiant->id),
            ]);
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
            $notifications->push([
                'id' => 'stage_fin_' . $stage->id,
                'type' => 'stage_fin_semaine',
                'title' => 'Stage bientôt terminé',
                'message' => $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' - Fin dans ' . $joursRestants . ' jour(s)',
                'icon' => 'clock',
                'color' => 'amber',
                'time' => $stage->date_fin,
                'url' => encrypted_route('stages.show', $stage),
            ]);
        }

        // 3. Stages récemment terminés (ces 7 derniers jours)
        $stagesTermines = Stage::where('date_fin', '<', now())
            ->where('date_fin', '>=', now()->subDays(7))
            ->with('etudiant')
            ->orderBy('date_fin', 'desc')
            ->get();

        foreach ($stagesTermines as $stage) {
            $notifications->push([
                'id' => 'stage_termine_' . $stage->id,
                'type' => 'stage_termine',
                'title' => 'Stage terminé',
                'message' => $stage->etudiant->nom . ' ' . $stage->etudiant->prenom . ' a terminé son stage',
                'icon' => 'check-circle',
                'color' => 'green',
                'time' => $stage->date_fin,
                'url' => encrypted_route('stages.show', $stage),
            ]);
        }

        // Trier par date décroissante
        return $notifications->sortByDesc('time')->values();
    }
}
