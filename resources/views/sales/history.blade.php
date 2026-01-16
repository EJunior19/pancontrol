@extends('layout.app')

@section('content')

{{-- ================= ENCABEZADO ================= --}}
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        🧾 Historial de ventas
    </h1>

    <a href="{{ route('sales.index') }}"
       class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-sm font-medium">
        ← Volver a venta
    </a>
</div>

{{-- ================= TABLA ================= --}}
<div class="bg-white rounded-xl shadow">

    <div class="max-h-[65vh] overflow-y-auto">
        <table class="w-full text-sm border-collapse">

            <thead class="bg-slate-100 text-slate-600 sticky top-0 z-10">
                <tr>
                    <th class="p-3 text-left">Fecha</th>
                    <th class="p-3 text-right">Total</th>
                    <th class="p-3 text-center">Pago</th>
                    <th class="p-3 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($sales as $sale)
                    <tr class="border-t hover:bg-slate-50">

                        <td class="p-3">
                            {{ $sale->sale_date->format('d/m/Y H:i') }}
                        </td>

                        <td class="p-3 text-right font-semibold text-emerald-600">
                            ₲ {{ number_format($sale->total_amount, 0, ',', '.') }}
                        </td>

                        <td class="p-3 text-center">
                            {{ ucfirst($sale->payment_method) }}
                        </td>

                        <td class="p-3 text-center">
                            <button
                                onclick="openReceiptModal({{ $sale->id }})"
                                class="inline-flex items-center gap-1 px-3 py-1.5
                                       rounded-md bg-slate-100 hover:bg-slate-200
                                       text-slate-700 text-xs font-medium">
                                🖨 Reimprimir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-400">
                            No hay ventas registradas
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>

{{-- ================= MODAL TICKET ================= --}}
<div id="receiptModal"
     class="fixed inset-0 z-50 flex items-center justify-center
            bg-black/50 opacity-0 pointer-events-none transition-opacity">

    <div class="bg-white w-[380px] max-h-[90vh]
                rounded-xl shadow-xl flex flex-col"
         onclick="event.stopPropagation()">

        {{-- HEADER --}}
        <div class="flex justify-between items-center px-4 py-3 border-b">
            <h2 class="font-semibold">🧾 Ticket de venta</h2>
            <button type="button"
                    onclick="closeReceiptModal()"
                    class="text-slate-400 hover:text-slate-600">
                ✕
            </button>
        </div>

        {{-- CONTENIDO --}}
        <div id="receiptContent"
             class="p-4 overflow-y-auto text-sm font-mono bg-slate-50">
            Cargando ticket...
        </div>

        {{-- FOOTER --}}
        <div class="px-4 py-3 border-t flex justify-end gap-2">
            <button type="button"
                    onclick="printTicket()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700
                           text-white rounded-lg text-sm">
                🖨 Imprimir
            </button>

            <button type="button"
                    onclick="closeReceiptModal()"
                    class="px-4 py-2 bg-slate-200 hover:bg-slate-300
                           rounded-lg text-sm">
                Cerrar
            </button>
        </div>
    </div>
</div>


{{-- ================= SCRIPTS ================= --}}
<script>
    let scrollY = 0;

    function openReceiptModal(saleId) {
        const modal = document.getElementById('receiptModal');
        const content = document.getElementById('receiptContent');

        // Guardar posición de scroll
        scrollY = window.scrollY;

        // Bloquear body correctamente
        document.body.classList.add('body-lock');
        document.body.style.top = `-${scrollY}px`;

        // Mostrar modal
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');

        content.innerHTML = 'Cargando ticket...';

        fetch(`/sales/${saleId}/receipt`)
            .then(res => res.text())
            .then(html => content.innerHTML = html)
            .catch(() => content.innerHTML = 'Error al cargar el ticket');
    }

    function closeReceiptModal() {
        const modal = document.getElementById('receiptModal');

        // Ocultar modal
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.classList.remove('opacity-100');

        // Restaurar body PERFECTAMENTE
        document.body.classList.remove('body-lock');
        document.body.style.top = '';

        // Volver al scroll original
        window.scrollTo(0, scrollY);
    }

    function printTicket() {
        const content = document.getElementById('receiptContent').innerHTML;

        const win = window.open('', '', 'width=400,height=600');
        win.document.write(`
            <html>
                <head>
                    <title>Ticket</title>
                    <style>
                        body {
                            font-family: monospace;
                            font-size: 12px;
                        }
                    </style>
                </head>
                <body>${content}</body>
            </html>
        `);
        win.document.close();
        win.focus();
        win.print();
        win.close();
    }
</script>






@endsection
