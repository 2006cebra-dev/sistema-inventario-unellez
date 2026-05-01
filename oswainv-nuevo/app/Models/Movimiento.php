<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_producto',
        'tipo',
        'cantidad',
        'motivo',
        'usuario_accion',
        'firma_digital',
        'user_id',
        'firma_hash',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'codigo_producto', 'codigo');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function generarFirma()
    {
        $data = $this->codigo_producto . $this->tipo . $this->cantidad . $this->user_id;
        return hash('sha256', $data . env('APP_KEY'));
    }

    public function esValida()
    {
        return $this->firma_hash === $this->generarFirma();
    }
}