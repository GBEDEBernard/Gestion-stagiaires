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

    public function compose(View $view)
    {
        if (!Auth::check()) {
            return;
        }

        $count = $this->notificationService->getUnreadCount();
        $recent = $this->notificationService->getUnreadNotifications()->take(5);

        $view->with([
            'notificationCount' => $count,
            'notifications' => $recent,
        ]);
    }
}
