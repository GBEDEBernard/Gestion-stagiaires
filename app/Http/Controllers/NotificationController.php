<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::visibleForUser(Auth::user())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = AppNotification::visibleForUser(Auth::user())
            ->unread()
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notification = AppNotification::visibleForUser(Auth::user())
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        // Rediriger vers l'URL de la notification
        return redirect($notification->url ?? route('dashboard'));
    }

    public function markAllAsRead()
    {
        AppNotification::visibleForUser(Auth::user())
            ->unread()
            ->update(['read_at' => now()]);

        return redirect()->back();
    }

    public function getUnreadJson()
    {
        $notifications = AppNotification::visibleForUser(Auth::user())
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'url' => route('notifications.markRead', $n->id),
                    'color' => $n->color,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'count' => AppNotification::visibleForUser(Auth::user())->unread()->count(),
        ]);
    }
}
