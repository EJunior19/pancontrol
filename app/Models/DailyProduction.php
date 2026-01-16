<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
class DailyProduction extends Model
{
    protected $table = 'daily_production';

    protected $fillable = [
        'production_date',
        'product_variant_id',
        'produced_quantity',
        'waste_quantity',
        'net_quantity',
        'unit',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // 👉 Insumos usados en esta producción
    public function supplies()
    {
        return $this->hasMany(DailyProductionSupply::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
