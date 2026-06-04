<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nick',
        'email',
        'cedula',
        'telefono',
        'password',
        'rol',
        'is_active',
        'profile_photo_path',
        'xp',
        'nivel',
        'current_streak',
        'longest_streak',
        'last_activity_date',
    ];

    protected $appends = ['display_name'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function bitacoraAccesos()
    {
        return $this->hasMany(BitacoraAcceso::class);
    }

    public function misiones()
    {
        return $this->hasMany(Mision::class);
    }

    public function requisiciones()
    {
        return $this->hasMany(Requisicion::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievement')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function xpLogs()
    {
        return $this->hasMany(XpLog::class);
    }

    public function getDisplayNameAttribute()
    {
        return $this->nick ?? $this->name;
    }

    public function tienePermiso($permiso)
    {
        if ($this->rol === 'desarrollador') return true;

        $rolesPermisos = \Illuminate\Support\Facades\Cache::get('roles_permisos', []);
        $permisos = $rolesPermisos[$this->rol] ?? [];

        return in_array($permiso, $permisos);
    }

    public function addXp($xp, $action, $description = null)
    {
        $this->xp = ($this->xp ?? 0) + $xp;
        $nivelNuevo = (int) floor($this->xp / 100) + 1;
        $subioNivel = $nivelNuevo > ($this->nivel ?? 1);
        $this->nivel = $nivelNuevo;
        $this->save();

        $this->xpLogs()->create([
            'xp' => $xp,
            'action' => $action,
            'description' => $description,
        ]);

        if ($subioNivel) {
            \App\Models\Notification::create([
                'user_id' => $this->id,
                'type' => 'level_up',
                'message' => "🎉 ¡Subiste al nivel {$this->nivel}!",
            ]);
        }

        $this->checkAndUnlockAchievements();
    }

    public function checkAndUnlockAchievements()
    {
        $stats = [
            'stock_entries' => $this->movimientos()->whereIn('tipo', ['entrada', 'salida'])->count(),
            'stock_exits' => $this->movimientos()->where('tipo', 'salida')->count(),
            'products_registered' => \App\Models\Producto::where('user_id', $this->id)->count(),
            'missions_completed' => \App\Models\Mision::where('user_id', $this->id)->where('status', 'completada')->count(),
            'requisitions_made' => $this->requisiciones()->count(),
            'login_streak' => $this->current_streak ?? 0,
            'chat_messages' => $this->sentMessages()->count(),
            'transfers_made' => \App\Models\Movimiento::where('user_id', $this->id)->where('tipo', 'transferencia')->count(),
        ];

        $achievements = \App\Models\Achievement::all();
        $desbloqueados = $this->achievements()->pluck('achievement_id')->toArray();

        foreach ($achievements as $ach) {
            if (in_array($ach->id, $desbloqueados)) continue;

            $valor = $stats[$ach->criteria_type] ?? 0;
            if ($valor >= $ach->criteria_value) {
                $this->achievements()->attach($ach->id, ['unlocked_at' => now()]);
                $this->addXp($ach->xp_reward, 'achievement_bonus', "Logro: {$ach->name}");
                \App\Models\Notification::create([
                    'user_id' => $this->id,
                    'type' => 'achievement_unlocked',
                    'message' => "🏆 ¡Logro desbloqueado: {$ach->name}!",
                ]);
            }
        }
    }
}
