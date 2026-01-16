@extends('layout.app')

@section('content')

<div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-2xl shadow">

    <!-- ENCABEZADO -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                ✏️ Editar variante
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Producto: <span class="font-semibold text-slate-700">{{ $variant->product->name }}</span>
            </p>

            <div class="mt-3 flex flex-wrap gap-2">
                <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">
                    ID variante: <b>{{ $variant->id }}</b>
                </span>

                @if($variant->barcode)
                    <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">
                        Barcode: <b>{{ $variant->barcode }}</b>
                    </span>
                @endif

                <span class="text-xs px-2 py-1 rounded-full {{ $variant->active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                    {{ $variant->active ? 'Activa' : 'Inactiva' }}
                </span>

                <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">
                    Stock: <b>{{ number_format($variant->stock_qty, $variant->allow_decimal ? 3 : 0, ',', '.') }}</b>
                </span>

                <span class="text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700">
                    Venta por: <b>{{ $variant->sale_unit === 'kg' ? 'Kilo' : 'Unidad' }}</b>
                </span>
            </div>
        </div>

        <a
            href="{{ route('product-variants.index') }}"
            class="text-sm px-3 py-2 rounded-lg border hover:bg-slate-50"
        >
            ← Volver
        </a>
    </div>

    {{-- ALERTAS --}}
    @if ($errors->any())
        <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-xl p-4">
            <b>Ups:</b> revisá los campos marcados.
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('product-variants.update', $variant) }}"
        class="space-y-5"
        onsubmit="return prepareVariantValues()"
    >
        @csrf
        @method('PUT')

        <!-- NOMBRE -->
        <div>
            <label class="text-sm font-medium text-slate-700">Nombre de la variante</label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $variant->name) }}"
                required
                class="mt-1 w-full rounded-lg border-slate-300"
            >
        </div>

        <!-- BARCODE -->
        <div>
            <label class="text-sm font-medium text-slate-700">Código de barras</label>
            <input
                type="text"
                name="barcode"
                value="{{ old('barcode', $variant->barcode) }}"
                class="mt-1 w-full rounded-lg border-slate-300"
            >
        </div>

        {{-- =====================
            PRECIO SEGÚN UNIDAD
        ===================== --}}

        @if($variant->sale_unit === 'unit')
            <!-- PRECIO POR UNIDAD -->
            <div>
                <label class="text-sm font-medium text-slate-700">Precio por unidad (Gs)</label>
                <input
                    type="text"
                    inputmode="numeric"
                    name="price_view"
                    value="{{ number_format(old('price', $variant->price ?? 0), 0, ',', '.') }}"
                    class="mt-1 w-full rounded-lg border-slate-300"
                    oninput="formatGs(this)"
                >
                <input type="hidden" name="price" id="price">
            </div>
        @else
            <!-- PRECIO POR KILO -->
            <div>
                <label class="text-sm font-medium text-slate-700">Precio por kilo (Gs)</label>
                <input
                    type="text"
                    inputmode="numeric"
                    name="price_per_kg_view"
                    value="{{ number_format(old('price_per_kg', $variant->price_per_kg ?? 0), 0, ',', '.') }}"
                    class="mt-1 w-full rounded-lg border-slate-300"
                    oninput="formatGs(this)"
                >
                <input type="hidden" name="price_per_kg" id="price_per_kg">
            </div>
        @endif

        <!-- STOCK -->
        <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-700">
            Stock actual:
            <b>{{ number_format($variant->stock_qty, $variant->allow_decimal ? 3 : 0, ',', '.') }}</b>
        </span>


        <!-- ACTIVA -->
        <div class="flex items-start gap-3 bg-slate-50 border rounded-xl p-4">
            <input
                type="checkbox"
                name="active"
                value="1"
                @checked(old('active', $variant->active))
            >
            <div>
                <p class="text-sm font-semibold">Variante activa para la venta</p>
                <p class="text-xs text-slate-500">
                    Si la desactivás, no aparece en la venta rápida.
                </p>
            </div>
        </div>

        <!-- BOTONES -->
        <div class="flex justify-end gap-3">
            <a
                href="{{ route('product-variants.index') }}"
                class="px-4 py-2 rounded-lg border"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold"
            >
                Guardar variante
            </button>
        </div>
    </form>
</div>

<script>
    function formatGs(input) {
        let value = (input.value || '').replace(/\D/g, '');
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function prepareVariantValues() {
        const priceView = document.querySelector('[name="price_view"]');
        const priceKgView = document.querySelector('[name="price_per_kg_view"]');

        if (priceView) {
            document.getElementById('price').value =
                priceView.value.replace(/\./g, '') || 0;
        }

        if (priceKgView) {
            document.getElementById('price_per_kg').value =
                priceKgView.value.replace(/\./g, '') || 0;
        }

        return true;
    }
</script>

@endsection
