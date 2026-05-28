<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoProveedor extends Model
{
    protected $table = 'producto_proveedor';

    protected $fillable = [
        'producto_id',
        'proveedor_id',
        'precio_costo',
        'codigo_proveedor',
    ];

    protected $casts = [
        'precio_costo' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
