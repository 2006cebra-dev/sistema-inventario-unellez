<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'stock',
        'marca',
        'categoria',
        'precio',
        'descripcion',
        'imagen',
        'fecha_vencimiento',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'fecha_vencimiento' => 'datetime:Y-m-d',
    ];

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'codigo_producto', 'codigo');
    }
}