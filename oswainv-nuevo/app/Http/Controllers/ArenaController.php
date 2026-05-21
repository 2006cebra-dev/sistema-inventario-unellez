<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Achievement;
use App\Models\XpLog;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;

class ArenaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $progress = GamificationService::getLevelProgress($user);
        $stats = GamificationService::getUserStats($user);

        $leaderboard = User::where('is_active', true)
            ->orderByDesc('xp')
            ->take(20)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'rol' => $u->rol,
                    'xp' => $u->xp,
                    'nivel' => $u->nivel,
                    'profile_photo_path' => $u->profile_photo_path,
                    'current_streak' => $u->current_streak,
                    'achievements_count' => $u->achievements()->count(),
                ];
            });

        $achievements = Achievement::all()->map(function ($ach) use ($user) {
            $unlocked = $user->achievements()->where('achievement_id', $ach->id)->exists();
            return [
                'id' => $ach->id,
                'name' => $ach->name,
                'description' => $ach->description,
                'icon' => $ach->icon,
                'xp_reward' => $ach->xp_reward,
                'unlocked' => $unlocked,
                'hidden' => $ach->hidden,
            ];
        });

        $recentXp = XpLog::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        $userRank = User::where('is_active', true)
            ->where('xp', '>', $user->xp)
            ->count() + 1;

        return view('arena.index', compact(
            'user', 'progress', 'stats', 'leaderboard',
            'achievements', 'recentXp', 'userRank'
        ));
    }

    public function leaderboard()
    {
        $leaderboard = User::where('is_active', true)
            ->orderByDesc('xp')
            ->get()
            ->map(function ($u, $i) {
                return [
                    'rank' => $i + 1,
                    'id' => $u->id,
                    'name' => $u->name,
                    'rol' => $u->rol,
                    'xp' => $u->xp,
                    'nivel' => $u->nivel,
                    'profile_photo_path' => $u->profile_photo_path,
                    'current_streak' => $u->current_streak,
                    'longest_streak' => $u->longest_streak,
                    'achievements_count' => $u->achievements()->count(),
                ];
            });

        return response()->json($leaderboard);
    }
}
