<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getRecent()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(15)
            ->get()
            ->map(function($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'is_read' => $n->is_read,
                    'link_url' => $n->link_url,
                    'time_ago' => $n->created_at->diffForHumans(),
                ];
            });

        $unreadCount = Notification::where('user_id', auth()->id())->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        if ($notification->link_url) {
            return redirect($notification->link_url);
        }

        return back();
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())->update(['is_read' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
