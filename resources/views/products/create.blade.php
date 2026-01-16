@extends('layout.app')

@section('content')

<!-- Título -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-800">
        Nuevo producto
    </h1>
    <p class="text-slate-500">
        Registrar producto base (producción o reventa)
    </p>
</div>

@if ($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 p-4 rounded-lg">
        {{ $errors->first() }}
    </div>
@endif

<form
    method="POST"
    action="{{ route('products.store') }}"
    class="max-w-xl bg-white p-6 rounded-2xl shadow space-y-5"
    onsubmit="prepareNumbers()"
>
    @csrf

    <!-- Nombre -->
    <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Nombre del producto
        </label>
        <input
            type="text"
            name="name"
            value="{{ old('name') }}"
            required
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >
    </div>

    <!-- Tipo -->
    <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Tipo de producto
        </label>
        <select
            name="type"
            required
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >
            <option value="">Seleccione</option>
            <option value="produced" @selected(old('type') === 'produced')>
                Producción propia
            </option>
            <option value="resale" @selected(old('type') === 'resale')>
                Reventa
            </option>
        </select>
    </div>

    <!-- Unidad de venta -->
    <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Unidad de venta (default)
        </label>
        <select
            name="unit"
            id="unitSelect"
            required
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >
            <option value="">Seleccione</option>
            <option value="kg" @selected(old('unit') === 'kg')>Kilogramo (kg)</option>
            <option value="unit" @selected(old('unit') === 'unit')>Unidad</option>
        </select>

        <p class="text-xs text-slate-400 mt-1">
            Esta unidad sirve como base para las variantes.
        </p>
    </div>

    <!-- Precio de referencia -->
    <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Precio de referencia (opcional)
        </label>
        <input
            type="text"
            name="reference_price"
            id="reference_price"
            inputmode="numeric"
            autocomplete="off"
            value="{{ old('reference_price') ? number_format(old('reference_price'), 0, ',', '.') : '' }}"
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >
        <p class="text-xs text-slate-400 mt-1">
            Solo informativo. El precio real se define en la variante.
        </p>
    </div>

    <!-- Control de stock -->
    <div>
        <label class="inline-flex items-center gap-2">
            <input
                type="checkbox"
                name="track_stock"
                value="1"
                @checked(old('track_stock', 1))
                class="rounded border-slate-300 text-emerald-600"
            >
            <span class="text-sm text-slate-600">
                Controlar stock y producción
            </span>
        </label>
    </div>

    <!-- Observaciones -->
    <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">
            Observaciones
        </label>
        <textarea
            name="notes"
            rows="3"
            class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
        >{{ old('notes') }}</textarea>
    </div>

    <!-- Estado -->
    <div>
        <label class="inline-flex items-center gap-2">
            <input
                type="checkbox"
                name="active"
                value="1"
                @checked(old('active', 1))
                class="rounded border-slate-300 text-emerald-600"
            >
            <span class="text-sm text-slate-600">
                Producto activo
            </span>
        </label>
    </div>

    <hr class="my-2">

    <!-- Tiene variantes -->
    <div class="flex items-start justify-between gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700">
                ¿Este producto tiene variantes?
            </label>
            <p class="text-xs text-slate-400">
                Ej: Chicle (menta/frutilla), gaseosa (lata/2L), pan (unidad/docena).
            </p>
        </div>

        <label class="inline-flex items-center gap-2 mt-1">
            <input
                type="checkbox"
                name="has_variants"
                id="hasVariants"
                value="1"
                @checked(old('has_variants'))
                class="rounded border-slate-300 text-emerald-600"
            >
            <span class="text-sm text-slate-700">Sí</span>
        </label>
    </div>

    <!-- Sección Variante Única -->
    <div id="singleVariantBox" class="border border-slate-200 rounded-2xl p-4 bg-slate-50 space-y-4">
        <div>
            <h3 class="text-sm font-semibold text-slate-700">
                Variante única (para vender directo)
            </h3>
            <p class="text-xs text-slate-400">
                Si NO tiene variantes, cargá acá precio/costo/stock y ya queda listo para vender con el lector.
            </p>
        </div>

        <!-- Barcode (lector) -->
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">
                Código de barras (opcional)
            </label>
            <input
                type="text"
                name="barcode"
                value="{{ old('barcode') }}"
                placeholder="Escanee el código (si tiene)"
                class="w-full text-lg tracking-widest rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            >
            <p class="text-xs text-slate-400 mt-1">
                Si el producto no tiene código, dejá vacío.
            </p>
        </div>

        <!-- Precio venta -->
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">
                Precio de venta (₲)
            </label>
            <input
                type="text"
                inputmode="numeric"
                autocomplete="off"
                name="price_view"
                id="price_view"
                value="{{ old('price_view', old('price') ? number_format(old('price'), 0, ',', '.') : '') }}"
                placeholder="Ej: 10.000"
                class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            >
            <input type="hidden" name="price" id="price_real" value="{{ old('price') }}">
            <p class="text-xs text-slate-400 mt-1" id="priceHelp">
                Para unidad, este será el precio por unidad.
            </p>
        </div>

        <!-- Costo -->
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">
                Costo actual (₲) (opcional)
            </label>
            <input
                type="text"
                inputmode="numeric"
                autocomplete="off"
                name="cost_view"
                id="cost_view"
                value="{{ old('cost_view', old('cost') ? number_format(old('cost'), 0, ',', '.') : '') }}"
                placeholder="Ej: 7.000"
                class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            >
            <input type="hidden" name="cost" id="cost_real" value="{{ old('cost') }}">
        </div>

        <!-- Stock -->
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">
                Stock inicial
            </label>
            <input
                type="number"
                name="stock_qty"
                id="stock_qty"
                min="0"
                step="1"
                value="{{ old('stock_qty', 0) }}"
                class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            >
            <p class="text-xs text-slate-400 mt-1" id="stockHelp">
                Para unidad: 1, 2, 3... (sin decimales).
            </p>
        </div>
    </div>

    <!-- Botón -->
    <div class="pt-2">
        <button
            type="submit"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold w-full"
        >
            Guardar producto
        </button>
    </div>

</form>

<script>
function formatGs(el) {
    let v = (el.value || '').replace(/\D/g, '');
    el.value = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function stripDots(v) {
    return (v || '').replace(/\./g, '').trim();
}

function updateUnitHints() {
    const unit = document.getElementById('unitSelect').value;
    const stock = document.getElementById('stock_qty');
    const stockHelp = document.getElementById('stockHelp');
    const priceHelp = document.getElementById('priceHelp');

    if (unit === 'kg') {
        stock.step = '0.001';
        stockHelp.textContent = 'Para kg: podés usar decimales (ej: 2.500).';
        priceHelp.textContent = 'Para kg, este será el precio por kilo.';
        // backend aceptará price_per_kg como fallback si querés, pero mandamos price igual y el controller lo decide
    } else {
        stock.step = '1';
        stockHelp.textContent = 'Para unidad: 1, 2, 3... (sin decimales).';
        priceHelp.textContent = 'Para unidad, este será el precio por unidad.';
    }
}

function toggleSingleVariantBox() {
    const hasVariants = document.getElementById('hasVariants').checked;
    const box = document.getElementById('singleVariantBox');

    // Si tiene variantes => ocultar "Única"
    box.classList.toggle('hidden', hasVariants);

    // Si ocultamos, no obligamos campos (en el backend ya validás condicional)
}

function prepareNumbers() {
    // reference_price
    const ref = document.getElementById('reference_price');
    if (ref) ref.value = stripDots(ref.value);

    // price/cost
    const priceView = document.getElementById('price_view');
    const costView  = document.getElementById('cost_view');

    document.getElementById('price_real').value = stripDots(priceView.value);
    document.getElementById('cost_real').value  = stripDots(costView.value);
}

document.addEventListener('DOMContentLoaded', () => {
    // Formateo en vivo
    const ref = document.getElementById('reference_price');
    const priceView = document.getElementById('price_view');
    const costView  = document.getElementById('cost_view');

    if (ref) ref.addEventListener('input', () => formatGs(ref));
    if (priceView) priceView.addEventListener('input', () => formatGs(priceView));
    if (costView)  costView.addEventListener('input', () => formatGs(costView));

    // Unidad (kg/unit)
    document.getElementById('unitSelect').addEventListener('change', updateUnitHints);
    updateUnitHints();

    // Variantes sí/no
    document.getElementById('hasVariants').addEventListener('change', toggleSingleVariantBox);
    toggleSingleVariantBox();
});
</script>

@endsection
