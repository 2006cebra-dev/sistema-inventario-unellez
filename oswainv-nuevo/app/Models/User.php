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
}
