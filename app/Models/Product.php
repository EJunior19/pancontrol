<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',          // productos por unidad
        'price_per_kg',   // productos vendidos por kilo
        'sale_unit',      // kg | unit
        'stock_qty',      // stock en kg o unidades
        'allow_decimal',  // true para kg
        'type',           // produced | resale
        'active',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'price_per_kg'  => 'decimal:2',
        'stock_qty'     => 'decimal:3',
        'allow_decimal' => 'boolean',
        'active'        => 'boolean',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function dailyProductions()
    {
        return $this->hasMany(DailyProduction::class);
    }

    /* =====================
     |  LÓGICA DE NEGOCIO
     ===================== */

    /**
     * Retorna el precio correcto según la unidad de venta
     */
    public function getSalePrice(): float
    {
        if ($this->sale_unit === 'kg') {
            return (float) ($this->price_per_kg ?? 0);
        }

        return (float) ($this->price ?? 0);
    }

    /**
     * Valida si hay stock suficiente
     */
    public function hasSufficientStock(float $quantity): bool
    {
        return $this->stock_qty >= $quantity;
    }

    /**
     * Descuenta stock del producto
     */
    public function decreaseStock(float $quantity): void
    {
        if (! $this->hasSufficientStock($quantity)) {
            throw new \Exception("Stock insuficiente para {$this->name}");
        }

        $this->stock_qty = round($this->stock_qty - $quantity, 3);
        $this->save();
    }

    /**
     * Incrementa stock (producción o compra)
     */
    public function increaseStock(float $quantity): void
    {
        $this->stock_qty = round($this->stock_qty + $quantity, 3);
        $this->save();
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

}
