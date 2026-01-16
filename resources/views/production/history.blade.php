@extends('layout.app')

@section('content')

{{-- ================= ENCABEZADO ================= --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <span class="text-2xl">📦</span>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Historial de producción
            </h1>
            <p class="text-sm text-slate-500">
                Producciones registradas por fecha y variante
            </p>
        </div>
    </div>

    <a href="{{ route('production.index') }}"
       class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300
              text-slate-700 font-medium transition">
        ← Volver
    </a>
</div>

{{-- ================= TABLA ================= --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">

    <div class="max-h-[70vh] overflow-y-auto overflow-x-auto">

        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Producto</th>
                    <th class="px-4 py-3 text-left">Variante</th>
                    <th class="px-4 py-3 text-right">Producido</th>
                    <th class="px-4 py-3 text-right">Merma</th>
                    <th class="px-4 py-3 text-right">Neto</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse ($rows as $r)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            {{ \Carbon\Carbon::parse($r->production_date)->format('d/m/Y') }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $r->producto }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $r->variante }}
                        </td>

                        <td class="px-4 py-3 text-right text-blue-600 font-semibold">
                            {{ number_format($r->produced_quantity, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-3 text-right text-red-600 font-semibold">
                            {{ number_format($r->waste_quantity, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-3 text-right text-emerald-600 font-bold">
                            {{ number_format($r->net_quantity, 3, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                            No hay producciones registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
