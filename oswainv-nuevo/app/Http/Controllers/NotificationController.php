<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        if ($request->unread_only) {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->take(50)->get();

        return response()->json($notifications);
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAsRead($id)
    {
        $notif = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function stream(Request $request)
    {
        $lastId = $request->input('last_id', 0);

        $notifications = Notification::where('user_id', Auth::id())
            ->where('id', '>', $lastId)
            ->latest()
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'last_id' => $notifications->max('id') ?? $lastId,
        ]);
    }

    public function sse()
    {
        $userId = Auth::id();

        return response()->stream(function () use ($userId) {
            $lastId = 0;
            while (true) {
                $notifications = Notification::where('user_id', $userId)
                    ->where('id', '>', $lastId)
                    ->latest()
                    ->get();

                if ($notifications->isNotEmpty()) {
                    $lastId = $notifications->max('id');
                    echo "data: " . $notifications->toJson() . "\n\n";
                } else {
                    echo "data: []\n\n";
                }

                ob_flush();
                flush();

                if (connection_aborted()) break;
                sleep(3);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);
    }
}
