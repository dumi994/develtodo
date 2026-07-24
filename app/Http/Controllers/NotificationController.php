<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->count();

        if (request()->wantsJson()) {
            return response()->json([
                'notifications' => $notifications,
                'unreadCount' => $unreadCount,
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead($id)
    {
        $notification = \App\Models\Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['read' => true]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        \App\Models\Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['ok' => true]);
    }
}
