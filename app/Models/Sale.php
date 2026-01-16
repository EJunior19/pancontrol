<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'sale_date',
        'total_amount',
        'payment_method',

        // 💰 Multimoneda
        'payment_currency',
        'exchange_rate',
        'paid_amount',

        // 🧾 Caja
        'cash_register_id',
    ];

    protected $casts = [
        'sale_date'       => 'datetime',
        'total_amount'    => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'exchange_rate'   => 'decimal:4',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    /* =====================
     |  LÓGICA DE NEGOCIO
     ===================== */

    /**
     * Calcula el total de la venta en base a sus ítems
     */
    public function calculateTotal(): float
    {
        return (float) $this->items()->sum('subtotal');
    }

    /**
     * Actualiza el total de la venta
     */
    public function updateTotal(): void
    {
        $this->update([
            'total_amount' => $this->calculateTotal(),
        ]);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

}
