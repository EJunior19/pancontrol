@extends('layout.app')

@section('content')

<div class="max-w-4xl mx-auto bg-white p-8 shadow border">

    {{-- ENCABEZADO --}}
    <div class="flex justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold">PANCONTROL</h1>
            <p class="text-sm">Panadería & Minimarket</p>
            <p class="text-sm">Av. San Martín 1234</p>
            <p class="text-sm">Salto del Guairá - Paraguay</p>
            <p class="text-sm">RUC: 1234567-8</p>
        </div>

        <div class="text-right">
            <h2 class="text-lg font-bold uppercase">Factura Legal</h2>
            <p class="text-sm">N° {{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p class="text-sm">Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <hr class="my-4">

    {{-- DATOS CLIENTE --}}
    <div class="mb-6">
        <h3 class="font-semibold mb-2">Datos del Cliente</h3>

        <div class="grid grid-cols-3 gap-4 text-sm">
            <div>
                <span class="font-medium">RUC:</span><br>
                {{ $sale->factura_ruc }}
            </div>

            <div>
                <span class="font-medium">Razón Social:</span><br>
                {{ $sale->factura_nombre }}
            </div>

            <div>
                <span class="font-medium">Dirección:</span><br>
                {{ $sale->factura_direccion }}
            </div>
        </div>
    </div>

    {{-- DETALLE --}}
    <table class="w-full text-sm border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2 text-left">Producto</th>
                <th class="border p-2 text-right">Cant.</th>
                <th class="border p-2 text-right">Precio</th>
                <th class="border p-2 text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $item)
                <tr>
                    <td class="border p-2">{{ $item->name }}</td>
                    <td class="border p-2 text-right">{{ $item->quantity }}</td>
                    <td class="border p-2 text-right">
                        ₲ {{ number_format($item->unit_price, 0, ',', '.') }}
                    </td>
                    <td class="border p-2 text-right">
                        ₲ {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALES --}}
    <div class="flex justify-end mt-6">
        <div class="w-64 text-sm">
            <div class="flex justify-between mb-1">
                <span>Subtotal:</span>
                <span>₲ {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between mb-1">
                <span>IVA (10%):</span>
                <span>₲ {{ number_format($sale->total * 0.1, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between font-bold text-lg border-t pt-2">
                <span>TOTAL:</span>
                <span>₲ {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <hr class="my-6">

    {{-- PIE --}}
    <div class="text-center text-sm">
        <p>Gracias por su compra</p>
        <p class="text-xs text-gray-500">Documento válido sin timbrado (demo)</p>
    </div>

    {{-- BOTONES --}}
    <div class="mt-6 flex justify-center gap-3 print:hidden">
        <button onclick="window.print()"
                class="px-4 py-2 bg-emerald-600 text-white rounded">
            Imprimir
        </button>

        <a href="{{ route('sales.index') }}"
           class="px-4 py-2 border rounded">
            Nueva venta
        </a>
    </div>

</div>

@endsection
