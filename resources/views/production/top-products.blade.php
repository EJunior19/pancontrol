@extends('layout.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <span class="text-3xl">🥖</span>
            <h1 class="text-2xl font-bold text-slate-800">
                Productos más vendidos
            </h1>
        </div>

        <!-- Botón volver -->
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2
                  px-4 py-2
                  bg-slate-200 hover:bg-slate-300
                  text-slate-700
                  rounded-lg
                  font-medium
                  transition">
            ← Volver
        </a>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Subheader -->
        <div class="px-6 py-4 bg-slate-50 border-b">
            <p class="text-sm text-slate-600">
                Ranking de productos con mayor salida en el día
            </p>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            Producto
                        </th>
                        <th class="px-6 py-3 text-right">
                            Cantidad vendida
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($products as $p)
                        <tr class="border-t hover:bg-slate-50 transition">
                            <td class="px-6 py-3 font-medium text-slate-800">
                                {{ $p->name }}
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-emerald-600">
                                {{ $p->cantidad_vendida }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-slate-400">
                                No hay ventas registradas hoy
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-slate-50 border-t text-xs text-slate-500">
            Actualizado automáticamente según las ventas del día
        </div>

    </div>
</div>

@endsection
