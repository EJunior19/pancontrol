@extends('layout.app')

@section('content')

{{-- ================= ENCABEZADO ================= --}}
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">Insumos</h1>
        <p class="text-slate-500">
            Control de stock de insumos de la panadería
        </p>
    </div>
        {{-- ACCIONES --}}
        <div class="flex flex-wrap items-center gap-2">

            {{-- FILTROS --}}
            <button
                type="button"
                onclick="filterLowStock()"
                class="px-4 py-2 rounded-lg bg-red-100 text-red-700
                    text-sm font-semibold hover:bg-red-200 transition">
                ⚠ Bajo stock
            </button>

            <button
                type="button"
                onclick="resetFilter()"
                class="px-4 py-2 rounded-lg bg-slate-200
                    hover:bg-slate-300 text-sm transition">
                Ver todos
            </button>

            {{-- SEPARADOR VISUAL --}}
            <span class="hidden sm:inline-block w-px h-6 bg-slate-300 mx-1"></span>

            {{-- ACCIÓN PRINCIPAL --}}
            <a href="{{ route('supplies.movements.create') }}"
            class="px-4 py-2 rounded-lg bg-emerald-600
                    hover:bg-emerald-700 text-white
                    text-sm font-semibold transition
                    inline-flex items-center gap-1">
                ➕ Ingreso de insumo
            </a>
            <a href="{{ route('supplies.create') }}"
            class="px-4 py-2 rounded-lg bg-emerald-600
                    hover:bg-emerald-700 text-white
                    text-sm font-semibold transition
                    inline-flex items-center gap-1">
                ➕ Crear insumo
            </a>
        </div>

</div>

{{-- ================= TABLA ================= --}}
<div class="bg-white rounded-2xl shadow overflow-hidden">

    <div class="max-h-[70vh] overflow-y-auto">
        <table class="min-w-full text-sm">

            <thead class="bg-slate-100 text-slate-600 uppercase text-xs
                          sticky top-0 z-10">
                <tr>
                    <th class="px-6 py-3 text-left">Insumo</th>
                    <th class="px-6 py-3 text-center">Stock</th>
                    <th class="px-6 py-3 text-center">Unidad</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody id="suppliesTable" class="divide-y">

            @forelse ($supplies as $supply)
                @php
                    $low = $supply->stock <= ($supply->min_stock ?? 0);
                @endphp

                <tr class="hover:bg-slate-50 transition
                           {{ $low ? 'low-stock bg-red-50/40' : '' }}">

                    {{-- NOMBRE --}}
                    <td class="px-6 py-3 font-medium text-slate-800">
                        {{ $supply->name }}
                    </td>

                    {{-- STOCK --}}
                    <td class="px-6 py-3 text-center font-semibold
                        {{ $low ? 'text-red-600' : 'text-slate-800' }}">
                        {{ number_format($supply->stock, 2, ',', '.') }}
                    </td>

                    {{-- UNIDAD --}}
                    <td class="px-6 py-3 text-center">
                        <span class="inline-block px-2 py-1 rounded-md
                                     text-xs font-semibold
                                     bg-slate-200 text-slate-700">
                            {{ strtoupper($supply->unit) }}
                        </span>
                    </td>

                    {{-- ESTADO --}}
                    <td class="px-6 py-3 text-center">
                        @if ($low)
                            <span class="inline-block bg-red-100 text-red-700
                                         px-3 py-1 rounded-full
                                         text-xs font-semibold">
                                Bajo stock
                            </span>
                        @else
                            <span class="inline-block bg-emerald-100
                                         text-emerald-700 px-3 py-1
                                         rounded-full text-xs font-semibold">
                                OK
                            </span>
                        @endif
                    </td>

                    {{-- ACCIONES --}}
                    <td class="px-6 py-3 text-center">
                        <button
                            type="button"
                            onclick="openAdjustModal({{ $supply->id }}, '{{ addslashes($supply->name) }}')"
                            class="px-3 py-1.5 rounded-md
                                   bg-slate-100 hover:bg-slate-200
                                   text-xs font-medium transition">
                            ⚙ Ajustar
                        </button>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5"
                        class="px-6 py-8 text-center text-slate-500">
                        No hay insumos registrados
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>
</div>

{{-- ================= MODAL AJUSTE ================= --}}
<div id="adjustModal"
     class="fixed inset-0 z-50 hidden
            items-center justify-center
            bg-black/50">

    <div class="bg-white w-[380px] rounded-xl shadow-xl p-5"
         onclick="event.stopPropagation()">

        <h2 class="text-lg font-semibold mb-1">
            Ajustar stock
        </h2>

        <p id="supplyName"
           class="text-sm text-slate-600 mb-4">
        </p>

        <form method="POST" action="{{ route('supplies.adjust') }}">
            @csrf

            <input type="hidden" name="supply_id" id="supplyId">

            {{-- Tipo --}}
            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">
                    Tipo de movimiento
                </label>
                <select name="type"
                        class="w-full border rounded-lg
                               px-3 py-2 text-sm">
                    <option value="in">Entrada</option>
                    <option value="out">Salida</option>
                </select>
            </div>

            {{-- Cantidad --}}
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Cantidad
                </label>
                <input type="number"
                       step="0.001"
                       min="0.001"
                       name="quantity"
                       required
                       class="w-full border rounded-lg
                              px-3 py-2 text-sm">
            </div>

            {{-- Botones --}}
            <div class="flex justify-end gap-2">
                <button type="button"
                        onclick="closeAdjustModal()"
                        class="px-4 py-2 bg-slate-200
                               hover:bg-slate-300
                               rounded-lg text-sm transition">
                    Cancelar
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-emerald-600
                               hover:bg-emerald-700
                               text-white rounded-lg
                               text-sm font-semibold transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= SCRIPTS ================= --}}
<script>
    const modal = document.getElementById('adjustModal');

    function openAdjustModal(id, name) {
        document.getElementById('supplyId').value = id;
        document.getElementById('supplyName').innerText = name;

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.body.style.overflow = 'hidden';
    }

    function closeAdjustModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        document.body.style.overflow = '';
    }

    function filterLowStock() {
        document.querySelectorAll('#suppliesTable tr').forEach(tr => {
            if (!tr.classList.contains('low-stock')) {
                tr.style.display = 'none';
            }
        });
    }

    function resetFilter() {
        document.querySelectorAll('#suppliesTable tr').forEach(tr => {
            tr.style.display = '';
        });
    }

    // cerrar modal al click fuera
    modal?.addEventListener('click', closeAdjustModal);
</script>

@endsection
