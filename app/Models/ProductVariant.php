<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'barcode',
        'sale_unit',      // kg | unit
        'price',          // precio por unidad
        'price_per_kg',   // precio por kilo
        'stock_qty',      // SOLO para reventa
        'allow_decimal',
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
     | RELACIONES
     ===================== */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* =====================
     | MÉTODOS DE NEGOCIO
     ===================== */

    /**
     * Indica si se vende por kilo
     */
    public function isSoldByKg(): bool
    {
        return $this->sale_unit === 'kg';
    }

    /**
     * Precio correcto según tipo de venta
     */
    public function getSalePrice(): float
    {
        return $this->isSoldByKg()
            ? (float) $this->price_per_kg
            : (float) $this->price;
    }

    /**
     * Calcula subtotal
     */
    public function calculateSubtotal(float $quantity): float
    {
        return round($quantity * $this->getSalePrice(), 2);
    }

    /**
     * Verifica stock disponible
     */
    public function hasSufficientStock(float $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Descuenta stock (SOLO REVENTA)
     */
    public function decreaseStock(float $quantity): void
    {
        if ($this->isProduced()) {
            throw new \Exception(
                "No se puede descontar stock manualmente de un producto producido"
            );
        }

        if (! $this->hasSufficientStock($quantity)) {
            throw new \Exception(
                "Stock insuficiente para {$this->name}"
            );
        }

        $this->stock_qty -= $quantity;
        $this->save();
    }

    /**
     * Incrementa stock (SOLO REVENTA)
     */
    public function increaseStock(float $quantity): void
    {
        if ($this->isProduced()) {
            throw new \Exception(
                "El stock de productos producidos se genera únicamente desde producción diaria"
            );
        }

        $this->stock_qty += $quantity;
        $this->save();
    }

    /* =====================
     | STOCK INTELIGENTE
     ===================== */

    /**
     * Determina si el producto es de producción
     */
    public function isProduced(): bool
    {
        return $this->product && $this->product->type === 'produced';
    }

    /**
     * Stock dinámico:
     * - Producción → suma de daily_production.net_quantity
     * - Reventa → stock_qty persistido
     */
    public function getStockAttribute()
    {
        if ($this->isProduced()) {
            return (float) DB::table('daily_production')
                ->where('product_variant_id', $this->id)
                ->sum('net_quantity');
        }

        return (float) $this->stock_qty;
    }
}
