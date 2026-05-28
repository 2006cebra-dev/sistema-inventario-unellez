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
        'stock_minimo',
        'stock_maximo',
        'unidad_medida',
        'marca',
        'categoria',
        'precio',
        'precio_costo',
        'descripcion',
        'imagen',
        'fecha_vencimiento',
        'proveedor_id',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_costo' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'fecha_vencimiento' => 'datetime:Y-m-d',
    ];

    public function getMargenAttribute(): ?float
    {
        if ($this->precio_costo && $this->precio_costo > 0) {
            return round((($this->precio - $this->precio_costo) / $this->precio_costo) * 100, 1);
        }
        return null;
    }

    public function getGananciaAttribute(): ?float
    {
        if ($this->precio_costo) {
            return round($this->precio - $this->precio_costo, 2);
        }
        return null;
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function scopeBajoStock($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'codigo_producto', 'codigo');
    }

    public function proveedor()
    {
        return $this->belongsTo(\App\Models\Proveedor::class, 'proveedor_id');
    }

    public function proveedores()
    {
        return $this->belongsToMany(\App\Models\Proveedor::class, 'producto_proveedor')
            ->withPivot('precio_costo', 'codigo_proveedor')
            ->withTimestamps();
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class, 'producto_id');
    }
}