@extends('layout.app')

@section('content')

<!-- ENCABEZADO -->
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <div>
        <h1 class="text-3xl font-bold text-slate-800">
            Variantes de productos
        </h1>
        <p class="text-slate-500">
            Códigos de barras, precios y stock por variante
        </p>
    </div>

    <div class="flex items-center gap-3">
        <!-- BOTÓN VOLVER (DINÁMICO) -->
        <a
            href="{{ url()->previous() }}"
            class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-sm"
        >
            ← Volver
        </a>

        <!-- NUEVA VARIANTE -->
        <a
            href="{{ route('product-variants.create', request('product_id') ? ['product_id' => request('product_id')] : []) }}"
            class="bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-xl font-semibold shadow inline-flex items-center gap-2"
        >
            + Nueva variante
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 bg-emerald-100 text-emerald-700 p-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<!-- TABLA -->
<div class="bg-white rounded-2xl shadow overflow-hidden">

    <table class="min-w-full text-sm">
        <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 text-left">Producto</th>
                <th class="px-6 py-3 text-left">Variante</th>
                <th class="px-6 py-3 text-left">Código</th>
                <th class="px-6 py-3 text-center">Unidad</th>
                <th class="px-6 py-3 text-right">Precio</th>
                <th class="px-6 py-3 text-right">Stock</th>
                <th class="px-6 py-3 text-center">Estado</th>
                <th class="px-6 py-3 text-right">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y">
        @forelse ($variants as $variant)
            <tr class="hover:bg-slate-50">

                <td class="px-6 py-3 font-medium text-slate-800">
                    {{ $variant->product->name }}
                </td>

                <td class="px-6 py-3 text-slate-700">
                    {{ $variant->name }}
                </td>

                <td class="px-6 py-3 text-slate-700 font-mono">
                    {{ $variant->barcode ?? '—' }}
                </td>

                <td class="px-6 py-3 text-center">
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $variant->sale_unit === 'kg'
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-slate-200 text-slate-700' }}">
                        {{ $variant->sale_unit === 'kg' ? 'Kg' : 'Unidad' }}
                    </span>
                </td>

                <td class="px-6 py-3 text-right font-semibold">
                    @if ($variant->sale_unit === 'kg')
                        ₲ {{ number_format($variant->price_per_kg, 0, ',', '.') }}
                        <span class="text-xs text-slate-500">/ kg</span>
                    @else
                        ₲ {{ number_format($variant->price, 0, ',', '.') }}
                    @endif
                </td>

                <td class="px-6 py-3 text-right">
                    {{ number_format(
                        $variant->stock,
                        $variant->allow_decimal ? 3 : 0,
                        ',',
                        '.'
                    ) }}
                    {{ $variant->sale_unit }}
                </td>


                <td class="px-6 py-3 text-center">
                    @if ($variant->active)
                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold">
                            Activa
                        </span>
                    @else
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                            Inactiva
                        </span>
                    @endif
                </td>

                <td class="px-6 py-3 text-right">
                    <a
                        href="{{ route('product-variants.edit', $variant) }}"
                        class="text-blue-600 hover:underline font-semibold"
                    >
                        Editar
                    </a>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-6 py-6 text-center text-slate-500">
                    No hay variantes registradas
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</div>

@endsection
