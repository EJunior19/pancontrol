<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyProductionSupply extends Model
{
    protected $fillable = [
        'daily_production_id',
        'supply_id',
        'quantity_used',
        'unit',
    ];

    // Producción a la que pertenece
    public function production()
    {
        return $this->belongsTo(DailyProduction::class, 'daily_production_id');
    }

    // Insumo usado
    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }
}
