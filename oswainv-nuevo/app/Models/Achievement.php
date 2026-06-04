<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'xp_reward',
        'criteria_type',
        'criteria_value',
        'hidden',
    ];

    protected function casts(): array
    {
        return [
            'hidden' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievement')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
