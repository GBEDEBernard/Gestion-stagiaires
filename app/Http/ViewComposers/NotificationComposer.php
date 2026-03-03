<?php

namespace App\Http\ViewComposers;

use App\Services\NotificationService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Composer les données de notification pour toutes les vues
     */
    public function compose(View $view)
    {
        // Générer les notifications automatiquement
        $this->notificationService->generateNotifications();

        // Récupérer les notifications non lues
        $notifications = $this->notificationService->getUnreadNotifications();
        $notificationCount = $this->notificationService->getUnreadCount();

        $view->with('notifications', $notifications);
        $view->with('notificationCount', $notificationCount);
    }
}
