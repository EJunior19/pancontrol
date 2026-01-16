<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'subtotal',
        'discount_amount',
    ];

    protected $casts = [
        'quantity'        => 'decimal:3',
        'unit_price'      => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    /* =====================
     | RELACIONES
     ===================== */

    /**
     * 🧾 Item pertenece a una venta
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * 📦 Item pertenece a una variante
     * (tu relación original)
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * ✅ Alias compatible (para eager loading y código más estándar)
     * Esto evita errores cuando usás: items.productVariant.product
     */
    public function productVariant()
    {
        return $this->variant();
    }

    /**
     * 📦 Producto base (a través de la variante)
     * OJO: NO existe product_id en sale_items, por eso esto debe ser "through".
     */
    public function product()
    {
        // Laravel: hasOneThrough(ModelFinal, ModelIntermedio, foreignKeyIntermedio, foreignKeyFinal, localKey, localKeyIntermedio)
        return $this->hasOneThrough(
            Product::class,
            ProductVariant::class,
            'id',         // product_variants.id
            'id',         // products.id
            'product_variant_id', // sale_items.product_variant_id
            'product_id'  // product_variants.product_id
        );
    }

    /* =====================
     | LÓGICA DE NEGOCIO
     ===================== */

    /**
     * Calcula el subtotal en base a cantidad y precio
     */
    public function calculateSubtotal(): float
    {
        return round(((float) $this->quantity) * ((float) $this->unit_price), 2);
    }

    /**
     * Recalcula y asigna el subtotal (uso interno)
     */
    public function setSubtotal(): void
    {
        $this->subtotal = $this->calculateSubtotal();
    }

    /**
     * Aplica un descuento al item
     */
    public function applyDiscount(float $amount): void
    {
        $this->discount_amount = max(0, round($amount, 2));

        $this->subtotal = max(
            0,
            round($this->calculateSubtotal() - (float) $this->discount_amount, 2)
        );
    }
}
