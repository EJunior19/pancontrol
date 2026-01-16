<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $fillable = [
        'name',
        'stock',
        'unit',
        'minimum_stock',   // 🔧 unificado con la BD y las vistas
    ];

    /**
     * Movimientos de insumo (entradas / salidas)
     */
    public function movements()
    {
        return $this->hasMany(SupplyMovement::class);
    }

    /**
     * Uso de insumos en producción diaria
     * (lo vamos a usar luego)
     */
    public function productionUsages()
    {
        return $this->hasMany(DailyProductionSupply::class);
    }
}
