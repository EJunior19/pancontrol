<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{
    protected $fillable = [
        'cash_register_id',
        'type',        // in | out
        'amount',
        'currency',    // PYG | USD | BRL
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /* =====================
     | RELACIONES
     ===================== */

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    /* =====================
     | MÉTODOS DE NEGOCIO
     ===================== */

    /**
     * Indica si es un ingreso
     */
    public function isIn(): bool
    {
        return $this->type === 'in';
    }

    /**
     * Indica si es un egreso
     */
    public function isOut(): bool
    {
        return $this->type === 'out';
    }

    /**
     * Scope para filtrar por moneda
     */
    public function scopeCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope para ingresos
     */
    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope para egresos
     */
    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }
}
