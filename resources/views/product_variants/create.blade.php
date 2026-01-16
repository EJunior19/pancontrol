@extends('layout.app')

@section('content')

<!-- Título -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-800">
        Nueva Variante de Producto
    </h1>
    <p class="text-slate-500">
        Escanee el código de barras y complete los datos de la variante
    </p>
</div>

{{-- ERRORES --}}
@if ($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 p-4 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ÉXITO --}}
@if (session('success'))
    <div class="mb-4 bg-emerald-100 text-emerald-700 p-4 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<form
    method="POST"
    action="{{ route('product-variants.store') }}"
    class="max-w-2xl bg-white p-6 rounded-2xl shadow"
    onsubmit="return prepareNumericFields()"
>

    @csrf

    <!-- Código de barras -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Código de barras
        </label>
        <input
            type="text"
            name="barcode"
            autofocus
            placeholder="Escanee el código (opcional)"
            value="{{ old('barcode') }}"
            class="w-full text-lg tracking-widest rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >
        <p class="text-xs text-slate-400 mt-1">
            El cursor se mantiene aquí para el lector
        </p>
    </div>

    <!-- Producto base -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Producto base
        </label>
        <select
            name="product_id"
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            required
        >
            <option value="">Seleccione el producto</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected((string)old('product_id') === (string)$product->id)>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Nombre variante -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Nombre de la variante
        </label>
        <input
            type="text"
            name="name"
            required
            value="{{ old('name') }}"
            placeholder="Ej: Pan francés / Docena / Integral"
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >
    </div>

    <!-- Unidad -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Unidad de venta
        </label>
        <select
            name="sale_unit"
            id="unitSelect"
            onchange="handleUnitChange()"
            required
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >
            <option value="unit" @selected(old('sale_unit', 'unit') === 'unit')>Por unidad</option>
            <option value="kg"   @selected(old('sale_unit') === 'kg')>Por kilo</option>
        </select>

        <input
            type="hidden"
            name="allow_decimal"
            id="allowDecimalHidden"
            value="{{ old('sale_unit','unit') === 'kg' ? 1 : 0 }}"
        >
    </div>

    <!-- Precio unidad -->
    <div id="priceUnitField" class="mb-4">
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Precio por unidad (₲)
        </label>
        <input
            type="text"
            inputmode="numeric"
            name="price_view"
            id="priceView"
            value="{{ old('price') !== null ? number_format((float)old('price'), 0, ',', '.') : '' }}"
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            placeholder="Ej: 1.500"
            oninput="formatGs(this)"
        >
        <input type="hidden" name="price" id="priceReal" value="{{ old('price') }}">
    </div>

    <!-- Precio kilo -->
    <div id="priceKgField" class="mb-4 hidden">
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Precio por kilo (₲)
        </label>
        <input
            type="text"
            inputmode="numeric"
            name="price_per_kg_view"
            id="priceKgView"
            value="{{ old('price_per_kg') !== null ? number_format((float)old('price_per_kg'), 0, ',', '.') : '' }}"
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            placeholder="Ej: 25.000"
            oninput="formatGs(this)"
        >
        <input type="hidden" name="price_per_kg" id="priceKgReal" value="{{ old('price_per_kg') }}">
    </div>

    <!-- Stock -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Stock inicial
        </label>
        <input
            type="number"
            name="stock_qty"
            id="stockQty"
            min="0"
            step="1"
            required
            value="{{ old('stock_qty', 0) }}"
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >
        <p class="text-xs text-slate-400 mt-1">
            Use decimales solo si vende por kilo (ej: 2.500)
        </p>
    </div>

    <!-- Botones -->
    <div class="flex justify-end gap-3">
        <a
            href="{{ route('product-variants.index') }}"
            class="px-5 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-100"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-xl font-semibold"
        >
            Guardar variante
        </button>
    </div>

</form>

<script>
function formatGs(input) {
    let value = input.value.replace(/\D/g, '');
    input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function handleUnitChange() {
    const unit = document.getElementById('unitSelect').value;

    document.getElementById('priceUnitField').classList.toggle('hidden', unit !== 'unit');
    document.getElementById('priceKgField').classList.toggle('hidden', unit !== 'kg');

    document.getElementById('allowDecimalHidden').value = (unit === 'kg') ? 1 : 0;

    const stock = document.getElementById('stockQty');
    stock.step = (unit === 'kg') ? '0.001' : '1';

    if (unit === 'unit') {
        document.getElementById('priceKgView').value = '';
        document.getElementById('priceKgReal').value = '';
    } else {
        document.getElementById('priceView').value = '';
        document.getElementById('priceReal').value = '';
    }
}

function prepareNumericFields() {
    const unit = document.getElementById('unitSelect').value;

    if (unit === 'unit') {
        document.getElementById('priceReal').value =
            document.getElementById('priceView').value.replace(/\./g, '');
        document.getElementById('priceKgReal').value = '';
    } else {
        document.getElementById('priceKgReal').value =
            document.getElementById('priceKgView').value.replace(/\./g, '');
        document.getElementById('priceReal').value = '';
    }

    return true; // ✅ ESTA LÍNEA ES LA CLAVE
}

document.addEventListener('DOMContentLoaded', handleUnitChange);
</script>

@endsection
