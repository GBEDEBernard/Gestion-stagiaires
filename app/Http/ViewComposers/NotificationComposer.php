<?php

namespace App\Http\ViewComposers;

use App\Services\NotificationService;
use App\Services\UrgentNotificationService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    protected NotificationService $notificationService;
    protected UrgentNotificationService $urgentService;

    public function __construct(NotificationService $notificationService, UrgentNotificationService $urgentService)
    {
        $this->notificationService = $notificationService;
        $this->urgentService = $urgentService;
    }

    public function compose(View $view)
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount();
        $recent = $this->notificationService->getUnreadNotifications()->take(5);
        $urgentNotifications = $this->urgentService->getActiveUrgentNotificationsForUser($user);

        $view->with([
            'notificationCount' => $count,
            'menuNotifications' => $recent,
            'urgentNotification' => $urgentNotifications->first(),
            'urgentNotifications' => $urgentNotifications,
            'urgentNotificationsCount' => $urgentNotifications->count(),
        ]);
    }
}
