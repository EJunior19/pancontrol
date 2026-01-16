@extends('layout.app')

@section('content')

<div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow">

    <!-- ENCABEZADO -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-800">
                Editar producto
            </h1>
            <p class="text-sm text-slate-500">
                Modificá los datos generales del producto
            </p>
        </div>

        <!-- BOTÓN VOLVER -->
        <a
            href="{{ route('products.index') }}"
            class="text-slate-600 hover:text-slate-800 text-sm font-semibold"
        >
            ← Volver
        </a>
    </div>

    <!-- FORMULARIO -->
    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        <!-- NOMBRE -->
        <div class="mb-4">
            <label class="text-sm text-slate-600">Nombre</label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $product->name) }}"
                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                required
            >
        </div>

        <!-- TIPO -->
        <div class="mb-4">
            <label class="text-sm text-slate-600">Tipo</label>
            <select
                name="type"
                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="produced" @selected($product->type === 'produced')>
                    Producido
                </option>
                <option value="resale" @selected($product->type === 'resale')>
                    Reventa
                </option>
            </select>
        </div>

        <!-- UNIDAD -->
        <div class="mb-4">
            <label class="text-sm text-slate-600">Unidad de venta</label>
            <select
                name="sale_unit"
                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="unit" @selected($product->sale_unit === 'unit')>
                    Unidad
                </option>
                <option value="kg" @selected($product->sale_unit === 'kg')>
                    Kilogramo
                </option>
            </select>
        </div>

        <!-- DECIMALES -->
        <div class="mb-4 flex items-center gap-2">
            <input
                type="checkbox"
                name="allow_decimal"
                value="1"
                class="rounded border-slate-300"
                @checked($product->allow_decimal)
            >
            <span class="text-sm text-slate-600">
                Permitir decimales (productos por kilo)
            </span>
        </div>

        <!-- ACTIVO -->
        <div class="mb-4 flex items-center gap-2">
            <input
                type="checkbox"
                name="active"
                value="1"
                class="rounded border-slate-300"
                @checked($product->active)
            >
            <span class="text-sm text-slate-600">
                Producto activo
            </span>
        </div>

        <!-- OBSERVACIONES -->
        <div class="mb-6">
            <label class="text-sm text-slate-600">Observaciones</label>
            <textarea
                name="notes"
                rows="3"
                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
            >{{ old('notes', $product->notes) }}</textarea>
        </div>

        <!-- ACCIONES -->
        <div class="flex justify-end gap-3">
            <a
                href="{{ route('products.index') }}"
                class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold"
            >
                Guardar cambios
            </button>
        </div>
    </form>

</div>

@endsection
