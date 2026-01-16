@extends('layout.app')

@section('content')

<!-- ENCABEZADO -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">
            Productos
        </h1>
        <p class="text-slate-500">
            Gestión de productos base y sus variantes
        </p>
    </div>

    <a
        href="{{ route('products.create') }}"
        class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-xl font-semibold shadow inline-flex items-center gap-2"
    >
        + Nuevo producto
    </a>
</div>

<!-- TABLA -->
<div class="bg-white rounded-2xl shadow overflow-hidden">

    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Producto</th>
                <th class="px-6 py-3 text-center">Tipo</th>
                <th class="px-6 py-3 text-center">Unidad</th>
                <th class="px-6 py-3 text-center">Variantes</th>
                <th class="px-6 py-3 text-center">Estado</th>
                <th class="px-6 py-3 text-right">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y">

        @forelse ($products as $product)
            <tr class="hover:bg-slate-50">

                <!-- PRODUCTO -->
                <td class="px-6 py-3 font-medium text-slate-800">
                    {{ $product->name }}
                </td>

                <!-- TIPO -->
                <td class="px-6 py-3 text-center">
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $product->type === 'produced'
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-blue-100 text-blue-700' }}">
                        {{ $product->type === 'produced' ? 'Producción' : 'Reventa' }}
                    </span>
                </td>

                <!-- UNIDAD -->
                <td class="px-6 py-3 text-center">
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $product->sale_unit === 'kg'
                            ? 'bg-indigo-100 text-indigo-700'
                            : 'bg-slate-200 text-slate-700' }}">
                        {{ $product->sale_unit === 'kg' ? 'Kg' : 'Unidad' }}
                    </span>
                </td>

                <!-- VARIANTES -->
                <td class="px-6 py-3 text-center font-semibold">
                    <span class="{{ $product->variants_count > 0 ? 'text-slate-800' : 'text-slate-400' }}">
                        {{ $product->variants_count ?? $product->variants->count() }}
                    </span>
                </td>

                <!-- ESTADO -->
                <td class="px-6 py-3 text-center">
                    @if ($product->active)
                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold">
                            Activo
                        </span>
                    @else
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                            Inactivo
                        </span>
                    @endif
                </td>

                <!-- ACCIONES -->
                <td class="px-6 py-3 text-right">
                    <div class="inline-flex items-center gap-3">

                        <!-- Ver variantes -->
                        <a
                            href="{{ route('product-variants.index', ['product_id' => $product->id]) }}"
                            class="text-slate-700 hover:underline font-semibold text-sm"
                        >
                            Ver variantes
                        </a>

                        <!-- Editar producto -->
                        <a
                            href="{{ route('products.edit', $product) }}"
                            class="text-blue-600 hover:underline font-semibold text-sm"
                        >
                            Editar
                        </a>

                        <!-- Agregar variante -->
                        <a
                            href="{{ route('product-variants.create', ['product_id' => $product->id]) }}"
                            class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded-lg text-xs font-semibold"
                        >
                            + Variante
                        </a>

                    </div>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-6 text-center text-slate-500">
                    No hay productos registrados
                </td>
            </tr>
        @endforelse

        </tbody>
    </table>

</div>

@endsection
