@extends('layout.app')

@section('content')

@php
    /**
     * Formateo correcto por moneda
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
@endphp

<!-- TÍTULO -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">
            Caja
        </h1>
        <p class="text-slate-500">
            Control de apertura, cierre y estado de la caja
        </p>
    </div>

    @if ($cashRegister)
        <a href="{{ route('cash.report.pdf', $cashRegister->id) }}"
           target="_blank"
           class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium">
            📄 PDF
        </a>
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

<!-- ESTADO DE CAJA -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    <!-- ESTADO -->
    <div class="bg-white rounded-2xl shadow p-6">
        <p class="text-sm text-slate-500 mb-2">Estado actual</p>

        @if ($cashRegister)
            <p class="text-2xl font-bold text-emerald-600">
                CAJA ABIERTA
            </p>
            <p class="text-xs text-slate-400 mt-1">
                Abierta el {{ $cashRegister->opened_at->format('d/m/Y H:i') }}
            </p>
        @else
            <p class="text-2xl font-bold text-red-600">
                CAJA CERRADA
            </p>
        @endif
    </div>

    <!-- SALDO INICIAL -->
    @if ($cashRegister)
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm text-slate-500 mb-2">Saldo inicial</p>

            <div class="space-y-1 text-sm">
                <p class="font-semibold text-slate-800">
                    {{ fmtMoney($cashRegister->opening_gs, 'PYG') }}
                </p>

                @if($cashRegister->opening_usd > 0)
                    <p class="text-slate-700">
                        {{ fmtMoney($cashRegister->opening_usd, 'USD') }}
                    </p>
                @endif

                @if($cashRegister->opening_brl > 0)
                    <p class="text-slate-700">
                        {{ fmtMoney($cashRegister->opening_brl, 'BRL') }}
                    </p>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- MOVIMIENTOS -->
@if ($cashRegister)
<div class="bg-white rounded-2xl shadow p-6 mb-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-4">
        Movimientos de caja
    </h2>

    <!-- CONTENEDOR SCROLL -->
    <div class="max-h-80 overflow-y-auto border rounded-lg">

        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 sticky top-0 z-10">
                <tr class="text-slate-600">
                    <th class="px-4 py-2 text-left">Hora</th>
                    <th class="px-4 py-2 text-left">Tipo</th>
                    <th class="px-4 py-2 text-left">Moneda</th>
                    <th class="px-4 py-2 text-right">Monto</th>
                    <th class="px-4 py-2 text-left">Detalle</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($movements as $m)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($m->created_at)->format('H:i') }}
                        </td>

                        <td class="px-4 py-2 font-semibold
                            {{ $m->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $m->type === 'in' ? 'Ingreso' : 'Egreso' }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $m->currency }}
                        </td>

                        <td class="px-4 py-2 text-right font-medium">
                            {{ fmtMoney($m->amount, $m->currency) }}
                        </td>

                        <td class="px-4 py-2 text-slate-600">
                            {{ $m->description }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                            Sin movimientos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endif

<!-- ACCIONES -->
<div class="bg-white rounded-2xl shadow p-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-4">
        Acciones
    </h2>

    <div class="flex flex-wrap gap-4">

        @if (! $cashRegister)
            <a href="{{ route('cash.open.form') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                Abrir caja
            </a>
        @else
            <a href="{{ route('sales.index') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                Ir a ventas
            </a>

            <a href="{{ route('cash.close.form') }}"
               class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                Cerrar caja
            </a>
        @endif

    </div>
</div>

@endsection
