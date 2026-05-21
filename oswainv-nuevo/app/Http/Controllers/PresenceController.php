<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PresenceController extends Controller
{
    public function heartbeat(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Store the user's last heartbeat with expiry (75 seconds TTL)
        Cache::put('presence_' . $user->id, now()->timestamp, 75);

        return response()->json(['ok' => true]);
    }

    public function online()
    {
        $users = \App\Models\User::where('rol', 'empleado')->get();
        $online = [];

        foreach ($users as $u) {
            $last = Cache::get('presence_' . $u->id);
            if ($last && (now()->timestamp - $last) < 60) {
                $online[] = $u->id;
            }
        }

        return response()->json([
            'online_ids' => $online,
            'count' => count($online)
        ]);
    }
}
