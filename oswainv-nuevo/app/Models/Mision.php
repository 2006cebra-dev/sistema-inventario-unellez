<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mision extends Model
{
    protected $table = 'misiones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_vencimiento',
        'xp_recompensa',
        'user_id',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}