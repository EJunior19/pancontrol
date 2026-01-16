@extends('layout.app')

@section('content')

{{-- ================= TÍTULO ================= --}}
<div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-800">
        Producción diaria
    </h1>
    <p class="text-slate-500">
        Registro y control de la producción del día
    </p>
</div>

{{-- ================= RESUMEN ================= --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- TOTAL PRODUCIDO --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <p class="text-sm text-slate-500 mb-2">
            Total producido
        </p>
        <p class="text-2xl font-bold text-slate-800">
            {{ number_format($totalProduced ?? 0, 2, ',', '.') }} Kg
        </p>
        <p class="text-xs text-slate-400 mt-1">
            Producción registrada del día
        </p>
    </div>

    {{-- TOTAL VENDIDO --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <p class="text-sm text-slate-500 mb-2">
            Total vendido
        </p>
        <p class="text-2xl font-bold text-emerald-600">
            {{ number_format($totalSold ?? 0, 2, ',', '.') }} Kg
        </p>
        <p class="text-xs text-slate-400 mt-1">
            Ventas realizadas con stock producido
        </p>
    </div>

    {{-- MERMA --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <p class="text-sm text-slate-500 mb-2">
            Merma
        </p>
        <p class="text-2xl font-bold text-red-600">
            {{ number_format($totalWaste ?? 0, 2, ',', '.') }} Kg
        </p>
        <p class="text-xs text-slate-400 mt-1">
            Diferencia no vendida / desperdicio
        </p>
    </div>

</div>

{{-- ================= ACCIONES ================= --}}
<div class="bg-white rounded-2xl shadow p-6">
    <h2 class="text-lg font-semibold text-slate-800 mb-4">
        Acciones
    </h2>

    <div class="flex flex-wrap gap-4">

        {{-- REGISTRAR PRODUCCIÓN --}}
        <a href="{{ route('production.create') }}"
           class="inline-flex items-center gap-2
                  bg-amber-600 hover:bg-amber-700
                  text-white px-5 py-2 rounded-lg
                  text-sm font-medium transition">
            ➕ Registrar producción
        </a>

        {{-- HISTÓRICO --}}
        <a href="{{ route('production.products-history') }}"
           class="inline-flex items-center gap-2
                  bg-slate-600 hover:bg-slate-700
                  text-white px-5 py-2 rounded-lg
                  text-sm font-medium transition">
            📈 Histórico de productos
        </a>

    </div>
</div>

@endsection
