<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'rif',
        'contacto',
        'telefono',
        'direccion',
        'logo',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->hasMany(\App\Models\Producto::class, 'proveedor_id');
    }

    public function productosPivot()
    {
        return $this->belongsToMany(\App\Models\Producto::class, 'producto_proveedor')
            ->withPivot('precio_costo', 'codigo_proveedor')
            ->withTimestamps();
    }
}
