@extends('layout.app')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow p-6">

    {{-- TÍTULO --}}
    <h1 class="text-2xl font-bold text-slate-800 mb-1">
        🥖 Registrar Producción
    </h1>

    <p class="text-slate-500 mb-6">
        Cargá la producción diaria y los insumos utilizados
    </p>

    {{-- ERRORES --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('production.store') }}">
        @csrf

        {{-- PRODUCTO --}}
        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1">
                Producto
            </label>

            <select id="product-select"
                    class="w-full rounded-lg border-slate-300">
                <option value="">Seleccione un producto</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- VARIANTE --}}
        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1">
                Variante producida
            </label>

            <select name="product_variant_id"
                    id="variant-select"
                    required
                    class="w-full rounded-lg border-slate-300">
                <option value="">Seleccione una variante</option>
            </select>
        </div>

        {{-- CANTIDAD PRODUCIDA --}}
        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1">
                Cantidad producida
            </label>

            <input type="number"
                   step="0.001"
                   min="0"
                   name="produced_quantity"
                   required
                   placeholder="Ej: 25.500"
                   class="w-full rounded-lg border-slate-300
                          focus:border-amber-500 focus:ring-amber-500">
        </div>

        {{-- MERMA --}}
        <div class="mb-6">
            <label class="block text-sm text-slate-600 mb-1">
                Merma
            </label>

            <input type="number"
                   step="0.001"
                   min="0"
                   name="waste_quantity"
                   value="0"
                   placeholder="Ej: 0.500"
                   class="w-full rounded-lg border-slate-300
                          focus:border-amber-500 focus:ring-amber-500">

            <p class="text-xs text-slate-400 mt-1">
                Cantidad desperdiciada o no apta para la venta
            </p>
        </div>

        {{-- INSUMOS --}}
        <h2 class="font-semibold mb-3 text-slate-700">
            Insumos utilizados
        </h2>

        <div id="supplies-wrapper" class="space-y-3 mb-4">

            {{-- FILA BASE --}}
            <div class="flex gap-2 items-center supply-row">
                <select name="supplies[0][supply_id]" required
                        class="flex-1 rounded-lg border-slate-300">
                    <option value="">Seleccione insumo</option>
                    @foreach ($supplies as $supply)
                        <option value="{{ $supply->id }}">
                            {{ $supply->name }} ({{ strtoupper($supply->unit) }})
                        </option>
                    @endforeach
                </select>

                <input type="number"
                       step="0.001"
                       min="0"
                       name="supplies[0][quantity_used]"
                       placeholder="Cantidad"
                       required
                       class="w-32 rounded-lg border-slate-300">
            </div>

        </div>

        {{-- AGREGAR INSUMO --}}
        <button type="button"
                onclick="addSupplyRow()"
                class="mb-6 text-sm font-medium text-amber-600 hover:text-amber-700">
            + Agregar otro insumo
        </button>

        {{-- BOTÓN --}}
        <button type="submit"
            class="w-full bg-emerald-600 hover:bg-emerald-700
                   text-white py-3 rounded-xl font-semibold transition">
            Registrar producción
        </button>
    </form>
</div>

{{-- ================= SCRIPTS ================= --}}
<script>
/* ===== INSUMOS DINÁMICOS ===== */
let supplyIndex = 1;

function addSupplyRow() {
    const wrapper = document.getElementById('supplies-wrapper');

    const row = document.createElement('div');
    row.className = 'flex gap-2 items-center';

    row.innerHTML = `
        <select name="supplies[${supplyIndex}][supply_id]"
                required
                class="flex-1 rounded-lg border-slate-300">
            <option value="">Seleccione insumo</option>
            @foreach ($supplies as $supply)
                <option value="{{ $supply->id }}">
                    {{ $supply->name }} ({{ strtoupper($supply->unit) }})
                </option>
            @endforeach
        </select>

        <input type="number"
               step="0.001"
               min="0"
               name="supplies[${supplyIndex}][quantity_used]"
               placeholder="Cantidad"
               required
               class="w-32 rounded-lg border-slate-300">
    `;

    wrapper.appendChild(row);
    supplyIndex++;
}

/* ===== PRODUCTO → VARIANTES ===== */
const variants = @json($variantsGroupedByProduct);

document.getElementById('product-select').addEventListener('change', function () {
    const productId = this.value;
    const variantSelect = document.getElementById('variant-select');

    variantSelect.innerHTML = '<option value="">Seleccione una variante</option>';

    if (!productId || !variants[productId]) return;

    variants[productId].forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.id;
        opt.textContent = v.name;
        variantSelect.appendChild(opt);
    });
});
</script>

@endsection
