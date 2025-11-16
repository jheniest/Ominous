<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->with(['relatedVideo', 'relatedComment', 'actionByUser'])
            ->recent()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function getUnread()
    {
        $notifications = auth()->user()
            ->notifications()
            ->unread()
            ->with(['relatedVideo', 'relatedComment', 'actionByUser'])
            ->recent()
            ->limit(10)
            ->get();

        $unreadCount = auth()->user()->notifications()->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notificação excluída com sucesso.');
    }

    public function destroyAll()
    {
        auth()->user()->notifications()->delete();

        return redirect()->back()->with('success', 'Todas as notificações foram excluídas.');
    }
}
