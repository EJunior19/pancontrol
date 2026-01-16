@extends('layout.app')

@section('content')

@php
    /**
     * Formateo seguro por moneda
     * - PYG: sin decimales
     * - USD / BRL: 2 decimales
     */
    function fmtMoney($value, $currency) {
        $value = (float) $value;

        if ($currency === 'PYG') {
            return '₲ ' . number_format($value, 0, ',', '.');
        }

        if ($currency === 'USD') {
            return 'USD ' . number_format($value, 2, ',', '.');
        }

        if ($currency === 'BRL') {
            return 'R$ ' . number_format($value, 2, ',', '.');
        }

        return number_format($value, 2, ',', '.');
    }

    function saldo($totals, $currency) {
        return ($totals[$currency]->total_in ?? 0) - ($totals[$currency]->total_out ?? 0);
    }
@endphp

<!-- ENCABEZADO -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">
            Resumen de Caja
        </h1>
        <p class="text-slate-500">
            Movimientos y estado actual de la caja
        </p>
    </div>

    @if ($cashRegister)
        <div class="flex gap-2">
            <a href="{{ route('cash.close.form') }}"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                🔒 Cerrar caja
            </a>
        </div>
    @endif
</div>

<!-- MENSAJES -->
@if ($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 p-4 rounded-lg">
        {{ $errors->first() }}
    </div>
@endif

@if (session('success'))
    <div class="mb-4 bg-emerald-100 text-emerald-700 p-4 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<!-- INFO SUPERIOR -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- ESTADO -->
    <div class="bg-white rounded-2xl shadow p-6">
        <p class="text-sm text-slate-500 mb-2">Estado</p>

        @if ($cashRegister)
            <p class="text-2xl font-bold text-emerald-600">
                CAJA ABIERTA
            </p>
            <p class="text-xs text-slate-400 mt-1">
                {{ $cashRegister->opened_at->format('d/m/Y H:i') }}
            </p>
        @else
            <p class="text-2xl font-bold text-red-600">
                CAJA CERRADA
            </p>
        @endif
    </div>

    <!-- APERTURA -->
    @if ($cashRegister)
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm text-slate-500 mb-2">Apertura de caja</p>

            <div class="space-y-1 text-sm">
                <p class="font-semibold text-slate-800">
                    {{ fmtMoney($cashRegister->opening_gs, 'PYG') }}
                </p>

                @if($cashRegister->opening_usd > 0)
                    <p>{{ fmtMoney($cashRegister->opening_usd, 'USD') }}</p>
                @endif

                @if($cashRegister->opening_brl > 0)
                    <p>{{ fmtMoney($cashRegister->opening_brl, 'BRL') }}</p>
                @endif
            </div>
        </div>
    @endif

    <!-- SALDO ACTUAL -->
    @if ($cashRegister)
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm text-slate-500 mb-2">Saldo actual</p>

            <div class="space-y-1 text-sm">
                <p class="font-semibold text-slate-800">
                    {{ fmtMoney(saldo($totals, 'PYG'), 'PYG') }}
                </p>

                <p>
                    {{ fmtMoney(saldo($totals, 'USD'), 'USD') }}
                </p>

                <p>
                    {{ fmtMoney(saldo($totals, 'BRL'), 'BRL') }}
                </p>
            </div>
        </div>
    @endif

</div>

<!-- MOVIMIENTOS -->
@if ($cashRegister)
<div class="bg-white rounded-2xl shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-4">
        Movimientos
    </h2>

    <div class="max-h-[420px] overflow-y-auto border rounded-lg">

        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 sticky top-0 z-10">
                <tr class="text-slate-600">
                    <th class="px-3 py-2 text-left">Hora</th>
                    <th class="px-3 py-2 text-left">Tipo</th>
                    <th class="px-3 py-2 text-left">Moneda</th>
                    <th class="px-3 py-2 text-right">Monto</th>
                    <th class="px-3 py-2 text-left">Detalle</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach ($movements as $m)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2">
                            {{ \Carbon\Carbon::parse($m->created_at)->format('H:i') }}
                        </td>

                        <td class="px-3 py-2 font-semibold
                            {{ $m->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $m->type === 'in' ? 'Ingreso' : 'Egreso' }}
                        </td>

                        <td class="px-3 py-2">
                            {{ $m->currency }}
                        </td>

                        <td class="px-3 py-2 text-right font-medium">
                            {{ fmtMoney($m->amount, $m->currency) }}
                        </td>

                        <td class="px-3 py-2 text-slate-600">
                            {{ $m->description }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endif

@endsection
