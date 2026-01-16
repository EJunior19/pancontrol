@extends('layout.app')

@section('content')

<!-- Título -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-slate-800">
        Panel de Control
    </h2>
    <p class="text-slate-500">
        Resumen de la actividad del día
    </p>
</div>

<!-- Tarjetas KPI -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

    <!-- Ventas del día -->
    <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-amber-500">
        <p class="text-sm text-slate-500">Ventas del día</p>
        <p class="text-3xl font-bold text-slate-800 mt-2">
            ₲ {{ number_format($todaySales ?? 0, 0, ',', '.') }}
        </p>
    </div>

    <!-- Productos activos -->
    <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-blue-500">
        <p class="text-sm text-slate-500">Productos activos</p>
        <p class="text-3xl font-bold text-slate-800 mt-2">
            {{ $productsCount ?? 0 }}
        </p>
    </div>

    <!-- Producción del día -->
    <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-slate-500">Producción de hoy</p>
        <p class="text-3xl font-bold text-slate-800 mt-2">
            {{ number_format($todayProduction ?? 0, 3, ',', '.') }} kg
        </p>
    </div>

    <!-- Estado de caja -->
    <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-emerald-500">
        <p class="text-sm text-slate-500">Estado de caja</p>

        @if (($cashStatus ?? 'CERRADA') === 'ABIERTA')
            <p class="text-xl font-semibold text-emerald-600 mt-3">
                ABIERTA
            </p>
        @else
            <p class="text-xl font-semibold text-red-600 mt-3">
                CERRADA
            </p>
        @endif
    </div>

</div>

<!-- Sección inferior -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10">

    <!-- Bienvenida -->
    <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-2">
            Bienvenido a PanControl
        </h3>
        <p class="text-slate-600 leading-relaxed">
            Sistema de gestión para panaderías que permite controlar
            ventas por kilo o unidad, producción diaria y caja en
            múltiples monedas de forma simple y confiable.
        </p>
    </div>

    <!-- Consejo -->
    <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow p-6 text-white">
        <h3 class="text-lg font-semibold mb-2">
            Consejo del día
        </h3>
        <p class="text-sm leading-relaxed">
            Registrá siempre la producción antes de vender para mantener
            un control real del stock y evitar faltantes.
        </p>
    </div>

</div>

@endsection
