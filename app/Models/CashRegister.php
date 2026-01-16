<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    protected $fillable = [
        'opened_at',
        'closed_at',
        'status',

        'opening_gs',
        'opening_usd',
        'opening_brl',

        'closing_gs',
        'closing_usd',
        'closing_brl',

        'difference_gs',
        'difference_usd',
        'difference_brl',

        'notes',
        'rate_usd','rate_brl',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',

        'opening_gs'  => 'decimal:2',
        'opening_usd' => 'decimal:2',
        'opening_brl' => 'decimal:2',

        'closing_gs'  => 'decimal:2',
        'closing_usd' => 'decimal:2',
        'closing_brl' => 'decimal:2',

        'difference_gs'  => 'decimal:2',
        'difference_usd' => 'decimal:2',
        'difference_brl' => 'decimal:2',
    ];

    /* =====================
     | RELACIONES
     ===================== */

    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    /* =====================
     | LÓGICA DE NEGOCIO
     ===================== */

    /**
     * Indica si la caja está abierta
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Retorna el total ingresado por moneda según movimientos
     */
    public function totalByCurrency(string $currency): float
    {
        return (float) $this->movements()
            ->where('currency', $currency)
            ->where('type', 'in')
            ->sum('amount');
    }

    /**
     * Retorna el total de egresos por moneda
     */
    public function totalOutByCurrency(string $currency): float
    {
        return (float) $this->movements()
            ->where('currency', $currency)
            ->where('type', 'out')
            ->sum('amount');
    }

    /**
     * Cierra la caja y calcula diferencias reales
     */
    public function close(array $closing, ?string $notes = null): void
    {
        if (! $this->isOpen()) {
            throw new \Exception('La caja ya está cerrada.');
        }

        $expectedGs  = $this->opening_gs  + $this->totalByCurrency('PYG') - $this->totalOutByCurrency('PYG');
        $expectedUsd = $this->opening_usd + $this->totalByCurrency('USD') - $this->totalOutByCurrency('USD');
        $expectedBrl = $this->opening_brl + $this->totalByCurrency('BRL') - $this->totalOutByCurrency('BRL');

        $this->update([
            'closed_at' => now(),
            'status'    => 'closed',

            'closing_gs'  => $closing['gs'],
            'closing_usd' => $closing['usd'],
            'closing_brl' => $closing['brl'],

            'difference_gs'  => round($closing['gs']  - $expectedGs, 2),
            'difference_usd' => round($closing['usd'] - $expectedUsd, 2),
            'difference_brl' => round($closing['brl'] - $expectedBrl, 2),

            'notes' => $notes,
        ]);
    }
}
