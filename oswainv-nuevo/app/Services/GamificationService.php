<?php

namespace App\Services;

use App\Models\User;
use App\Models\XpLog;
use App\Models\Achievement;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    const XP_VALUES = [
        'stock_entry' => 5,
        'stock_exit' => 5,
        'product_created' => 15,
        'mission_completed' => 25,
        'requisition_approved' => 20,
        'login_streak' => 10,
        'chat_message' => 2,
        'transfer_made' => 30,
    ];

    const XP_PER_LEVEL = 100;

    public static function addXp(User $user, string $action, ?string $description = null, ?int $customXp = null): void
    {
        $xp = $customXp ?? (self::XP_VALUES[$action] ?? 5);

        DB::transaction(function () use ($user, $action, $xp, $description) {
            XpLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'xp' => $xp,
                'description' => $description,
            ]);

            $user->xp += $xp;
            $newLevel = (int) floor($user->xp / self::XP_PER_LEVEL) + 1;

            if ($newLevel > $user->nivel) {
                $user->nivel = $newLevel;
                $user->save();

                self::notifyLevelUp($user, $newLevel);
            } else {
                $user->save();
            }

            self::checkAchievements($user);
        });
    }

    public static function checkStreak(User $user): void
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        if ($user->last_activity_date === $today) return;

        if ($user->last_activity_date === $yesterday) {
            $user->current_streak++;
            if ($user->current_streak > $user->longest_streak) {
                $user->longest_streak = $user->current_streak;
            }
            $user->last_activity_date = $today;
            $user->save();

            self::addXp($user, 'login_streak', "Racha de {$user->current_streak} días");

            if ($user->current_streak % 7 === 0) {
                self::addXp($user, 'login_streak', "¡{$user->current_streak} días seguidos! Bonus +30", 30);
            }
        } else {
            $user->current_streak = 1;
            $user->last_activity_date = $today;
            $user->save();
        }
    }

    public static function checkAchievements(User $user): void
    {
        $achievements = Achievement::all();
        $unlockedIds = $user->achievements()->pluck('achievement_id')->toArray();

        $stats = self::getUserStats($user);

        foreach ($achievements as $ach) {
            if (in_array($ach->id, $unlockedIds)) continue;

            $value = $stats[$ach->criteria_type] ?? 0;
            if ($value >= $ach->criteria_value) {
                $user->achievements()->attach($ach->id, ['unlocked_at' => now()]);
                self::addXp($user, 'achievement_bonus', "Logro: {$ach->name}", $ach->xp_reward);
                self::notifyAchievement($user, $ach);
            }
        }
    }

    public static function getUserStats(User $user): array
    {
        return [
            'stock_entries' => XpLog::where('user_id', $user->id)->where('action', 'stock_entry')->count(),
            'stock_exits' => XpLog::where('user_id', $user->id)->where('action', 'stock_exit')->count(),
            'products_registered' => XpLog::where('user_id', $user->id)->where('action', 'product_created')->count(),
            'missions_completed' => XpLog::where('user_id', $user->id)->where('action', 'mission_completed')->count(),
            'requisitions_made' => \App\Models\Requisicion::where('user_id', $user->id)->count(),
            'login_streak' => $user->longest_streak,
            'chat_messages' => XpLog::where('user_id', $user->id)->where('action', 'chat_message')->count(),
            'transfers_made' => XpLog::where('user_id', $user->id)->where('action', 'transfer_made')->count(),
        ];
    }

    private static function notifyLevelUp(User $user, int $level): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'level_up',
            'title' => "🎖️ ¡Subiste al nivel {$level}!",
            'message' => "Con {$user->xp} XP has alcanzado un nuevo nivel.",
            'icon' => 'bi-star-fill text-warning',
            'link' => route('arena.index'),
        ]);
    }

    private static function notifyAchievement(User $user, Achievement $ach): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'achievement_unlocked',
            'title' => "🏆 Logro desbloqueado: {$ach->name}",
            'message' => $ach->description,
            'icon' => 'bi-trophy-fill text-warning',
            'link' => route('arena.index'),
        ]);
    }

    public static function getLevelProgress(User $user): array
    {
        $xpInLevel = $user->xp % self::XP_PER_LEVEL;
        return [
            'level' => $user->nivel,
            'xp' => $user->xp,
            'xp_in_level' => $xpInLevel,
            'xp_for_next' => self::XP_PER_LEVEL,
            'progress' => $xpInLevel / self::XP_PER_LEVEL * 100,
        ];
    }
}
