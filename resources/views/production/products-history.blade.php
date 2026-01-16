@extends('layout.app')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            📦 Historial de productos
        </h1>
        <p class="text-sm text-slate-500">
            Movimientos de producción registrados
        </p>
    </div>

    <a href="{{ route('dashboard') }}"
       class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700">
        ← Volver
    </a>
</div>

<div class="bg-white rounded-2xl shadow overflow-hidden">

    <div class="max-h-[65vh] overflow-y-auto">
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
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($r->production_date)->format('d/m/Y') }}
                        </td>

                        <td class="px-4 py-2 font-medium">
                            {{ $r->product }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $r->variant }}
                        </td>

                        <td class="px-4 py-2 text-right text-blue-600">
                            {{ number_format($r->produced_quantity, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-2 text-right text-red-600">
                            {{ number_format($r->waste_quantity, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-2 text-right font-semibold text-emerald-700">
                            {{ number_format($r->net_quantity, 3, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">
                            No hay registros de producción.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 bg-slate-50 text-sm text-slate-600">
        Mostrando {{ $rows->count() }} registros
    </div>

</div>

@endsection
