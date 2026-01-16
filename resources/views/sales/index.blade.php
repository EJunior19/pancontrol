@extends('layout.app')

@section('content')

<div class="max-w-3xl mx-auto bg-white p-6 rounded-2xl shadow">

    <h1 class="text-2xl font-bold text-slate-800 mb-4">
        Venta rápida
    </h1>

    @if ($errors->any())
        <div class="mb-3 bg-red-100 text-red-700 p-3 rounded">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- ESCÁNER -->
    <div class="mb-6">
        <label class="text-sm text-slate-600">
            Código de barras
        </label>
        <input
            id="barcode"
            type="text"
            autocomplete="off"
            inputmode="numeric"
            class="w-full text-lg rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            placeholder="Escaneá el producto y presioná Enter"
        >
        <p id="scanMsg" class="mt-2 text-xs text-slate-500"></p>
    </div>

    <!-- CARRITO -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-700 mb-3">
            Productos
        </h2>

        <table class="w-full text-sm border rounded-lg overflow-hidden">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="p-2 text-left">Producto</th>
                    <th class="p-2 text-right">Cantidad</th>
                    <th class="p-2 text-right">Precio</th>
                    <th class="p-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody id="cartBody">
                <tr>
                    <td colspan="4" class="p-4 text-center text-slate-400">
                        Escaneá un producto para comenzar
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

<!-- TOTAL -->
<div class="mb-6">
    <div class="flex justify-between items-center">
        <span class="text-lg font-semibold text-slate-700">TOTAL</span>

        <span id="grandTotal" class="text-2xl font-bold text-emerald-600">
            ₲ 0
        </span>
    </div>

    <!-- equivalencias -->
    <div class="mt-2 text-sm text-slate-600 space-y-1">
        <div id="eqUsd"></div>
        <div id="eqBrl"></div>
    </div>
</div>

    <!-- FORM -->
    <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
        @csrf

        <input type="hidden" name="items" id="itemsInput">
        <input type="hidden" name="paid_amount" id="paid_amount">

        <input type="hidden" name="tipo_comprobante" id="tipo_comprobante" value="ticket">
        <input type="hidden" name="factura_ruc" id="factura_ruc_input">
        <input type="hidden" name="factura_nombre" id="factura_nombre_input">
        <input type="hidden" name="factura_direccion" id="factura_direccion_input">



        <div class="mb-4">
            <label class="text-sm text-slate-600">
                Forma de pago
            </label>
            <select
                name="payment_method"
                class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            >
                <option value="efectivo">Efectivo</option>
                <option value="transferencia">Transferencia</option>
                <option value="qr">QR</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="text-sm text-slate-600">Moneda de pago</label>

            <select
                name="payment_currency"
                id="payment_currency"
                class="w-full rounded-lg border-slate-300 focus:border-amber-500 focus:ring-amber-500"
            >
                <option value="PYG">Guaraníes (₲)</option>
                <option value="USD">Dólares ($)</option>
                <option value="BRL">Reales (R$)</option>
            </select>

            <p class="text-xs text-slate-500 mt-2">
                Si es USD o BRL, el sistema usa el tipo de cambio definido al abrir la caja.
            </p>
        </div>

        <input type="hidden" name="exchange_rate" id="exchange_rate" value="1">
        <div id="modalVuelto" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">

                <h2 class="text-xl font-bold text-slate-800 mb-4">
                    💰 Cobro
                </h2>

                <div class="mb-3 text-lg">
                    Total a pagar:
                    <b id="totalCobro" class="text-emerald-600"></b>
                </div>

                <div class="mb-3">
                    <label class="text-sm text-slate-600">Cliente paga</label>
                    <input
                        id="pagaInput"
                        type="text"
                        min="0"
                        class="w-full rounded-lg border-slate-300 text-lg"
                        oninput="calcularVuelto()"
                    >
                </div>

                <div class="mb-4 text-lg">
                    Vuelto:
                    <b id="vueltoOutput" class="text-blue-600">₲ 0</b>
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        onclick="cerrarModalVuelto()"
                        class="px-4 py-2 border rounded-lg"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        onclick="confirmarCobro()"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg"
                    >
                        Continuar
                    </button>
                </div>

            </div>
        </div>


        <button
            type="button"
            onclick="abrirModalVuelto()"
            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold"
        >
            Confirmar venta
        </button>
    </form>

</div>

<!-- ================= MODAL COMPROBANTE ================= -->
<div id="modalComprobante" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">

        <h2 class="text-xl font-bold text-slate-800 mb-4">
            Tipo de comprobante
        </h2>

        <label class="flex items-center gap-2 mb-3">
            <input type="radio" name="tipo" value="ticket" checked>
            🧾 Ticket común
        </label>

        <label class="flex items-center gap-2 mb-5">
            <input type="radio" name="tipo" value="factura">
            📄 Factura legal
        </label>

        <div class="flex justify-end gap-2">
            <button onclick="cerrarModalComprobante()" class="px-4 py-2 border rounded-lg">
                Cancelar
            </button>

            <button onclick="continuarVenta()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg">
                Continuar
            </button>
        </div>

    </div>
</div>
<!-- ================= MODAL DATOS FACTURA ================= -->
<div id="modalFactura" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">

        <h2 class="text-xl font-bold text-slate-800 mb-4">
            Datos para Factura
        </h2>

        <div class="mb-3">
            <label class="text-sm text-slate-600">RUC</label>
            <input id="factura_ruc_modal"
                   type="text"
                   class="w-full rounded-lg border-slate-300"
                   placeholder="Ej: 1234567-8"
                   onblur="buscarClientePorRuc()">
        </div>

        <div class="mb-3">
            <label class="text-sm text-slate-600">Nombre / Razón Social</label>
            <input id="factura_nombre"
                   type="text"
                   class="w-full rounded-lg border-slate-300"
                   placeholder="Nombre del cliente">
        </div>

        <div class="mb-5">
            <label class="text-sm text-slate-600">Dirección</label>
            <input id="factura_direccion"
                   type="text"
                   class="w-full rounded-lg border-slate-300"
                   placeholder="Dirección fiscal">
        </div>

        <div class="flex justify-end gap-2">
            <button onclick="cerrarModalFactura()"
                    class="px-4 py-2 border rounded-lg">
                Cancelar
            </button>

            <button onclick="confirmarFactura()"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg">
                Continuar
            </button>
        </div>

    </div>
</div>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const barcode      = document.getElementById('barcode');
const cartBody     = document.getElementById('cartBody');
const grandTotalEl = document.getElementById('grandTotal');
const itemsInput   = document.getElementById('itemsInput');
const scanMsg      = document.getElementById('scanMsg');
const modal        = document.getElementById('modalComprobante');

const eqUsd = document.getElementById('eqUsd');
const eqBrl = document.getElementById('eqBrl');
const paymentCurrencyEl = document.getElementById('payment_currency');

// ✅ estos valores vienen desde la caja abierta (los vas a enviar desde el controller)
const rateUSD = Number("{{ $cashRegister->rate_usd ?? 0 }}"); // ₲ por 1 USD
const rateBRL = Number("{{ $cashRegister->rate_brl ?? 0 }}"); // ₲ por 1 BRL


let cart = {};

// ===============================
// ALERTA BONITA DE STOCK
// ===============================
function alertaStock(disponible) {
    Swal.fire({
        icon: 'warning',
        title: 'Stock insuficiente',
        text: `Disponible: ${disponible}`,
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        background: '#fffbea',
        iconColor: '#f59e0b',
        didClose: () => {
            // ✅ cuando desaparece el alerta, listo para escanear otra vez
            barcode.value = '';
            barcode.focus();
            scanMsg.textContent = '';
        }
    });
}

// ✅ ALERTA MEJORADO: NO ENCONTRADO
function alertaNoEncontrado() {
    Swal.fire({
        icon: 'error',
        title: 'Producto no encontrado',
        text: 'Verificá el código e intentá de nuevo',
        showConfirmButton: false,
        timer: 1200,               // un poquito más rápido
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        iconColor: '#dc2626',
        didOpen: () => {
            // opcional: sonido de error (si no querés, borrá esta línea)
            // new Audio('/sounds/error.mp3').play();
        },
        didClose: () => {
            // ✅ cuando desaparece el alerta -> limpia y enfoca
            barcode.value = '';
            barcode.focus();
            scanMsg.textContent = '';
        }
    });
}

// ===============================
// FORMATO GUARANÍ
// ===============================
function gs(n) {
    return new Intl.NumberFormat('es-PY').format(n);
}
function money(n, dec = 2) {
    return new Intl.NumberFormat('es-PY', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
    }).format(n);
}

function renderEquivalents(totalGs) {
    // USD
    if (!rateUSD || rateUSD <= 0) {
        eqUsd.innerHTML = `USD: <span class="text-red-600">Definí el TC USD al abrir caja</span>`;
    } else {
        const usd = totalGs / rateUSD;
        eqUsd.innerHTML = `USD: <b>$ ${money(usd, 2)}</b> <span class="text-xs text-slate-400">(TC ₲ ${gs(rateUSD)} / 1)</span>`;
    }

    // BRL
    if (!rateBRL || rateBRL <= 0) {
        eqBrl.innerHTML = `BRL: <span class="text-red-600">Definí el TC BRL al abrir caja</span>`;
    } else {
        const brl = totalGs / rateBRL;
        eqBrl.innerHTML = `BRL: <b>R$ ${money(brl, 2)}</b> <span class="text-xs text-slate-400">(TC ₲ ${gs(rateBRL)} / 1)</span>`;
    }
}

// ===============================
// RENDER CARRITO
// ===============================
function renderCart() {
    cartBody.innerHTML = '';
    let total = 0;

    Object.values(cart).forEach(item => {
        total += item.subtotal;

        cartBody.innerHTML += `
            <tr class="border-t">
                <td class="p-2">${item.name}</td>
                <td class="p-2 text-right">
                    <input
                        type="number"
                        step="${item.allow_decimal ? '0.001' : '1'}"
                        min="${item.allow_decimal ? '0.001' : '1'}"
                        value="${item.quantity}"
                        class="w-24 text-right border rounded px-1"
                        onchange="updateQty(${item.variant_id}, this.value)"
                    >
                </td>
                <td class="p-2 text-right">₲ ${gs(item.unit_price)}</td>
                <td class="p-2 text-right font-semibold">₲ ${gs(item.subtotal)}</td>
            </tr>
        `;
    });

    if (Object.keys(cart).length === 0) {
        cartBody.innerHTML = `
            <tr>
                <td colspan="4" class="p-4 text-center text-slate-400">
                    Escaneá un producto para comenzar
                </td>
            </tr>
        `;
    }

    grandTotalEl.textContent = `₲ ${gs(total)}`;
    itemsInput.value = JSON.stringify(cart);
    renderEquivalents(total);

}

// ===============================
// LOOKUP PRODUCTO
// ===============================
async function lookup(barcodeValue) {
    scanMsg.textContent = 'Buscando producto...';

    try {
        const res = await fetch(`{{ route('sales.lookup') }}?barcode=${encodeURIComponent(barcodeValue)}`);
        if (!res.ok) throw new Error('Producto no encontrado');

        const p = await res.json();

        const qty = p.allow_decimal ? 0.001 : 1;
        const unitPrice = p.allow_decimal ? p.price_per_kg : p.price;

        if (!cart[p.id]) {

            if (qty > p.stock_qty) {
                alertaStock(p.stock_qty);
                return;
            }

            cart[p.id] = {
                variant_id: p.id,
                name: p.name,
                quantity: qty,
                unit_price: unitPrice,
                subtotal: qty * unitPrice,
                allow_decimal: p.allow_decimal,
                stock_qty: p.stock_qty
            };

        } else {

            const nuevaCantidad = cart[p.id].quantity + qty;

            if (nuevaCantidad > cart[p.id].stock_qty) {
                alertaStock(cart[p.id].stock_qty);
                return;
            }

            cart[p.id].quantity = nuevaCantidad;
            cart[p.id].subtotal = nuevaCantidad * unitPrice;
        }

        renderCart();
        scanMsg.textContent = '';
        barcode.value = '';
        barcode.focus();
        new Audio('/sounds/beep.mp3').play();

    } catch (e) {
        // ✅ alerta rápida + limpia input al cerrar
        alertaNoEncontrado();
    }
}

// ===============================
// EVENTOS TECLADO
// ===============================
barcode.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        e.preventDefault();
        const code = barcode.value.trim();
        if (!code) {
            barcode.value = '';
            barcode.focus();
            return;
        }
        lookup(code);
    }

    if (e.key === 'Escape') {
        barcode.value = '';
        scanMsg.textContent = '';
    }
});

barcode.focus();

// ===============================
// ACTUALIZAR CANTIDAD MANUAL
// ===============================
function updateQty(variantId, value) {
    if (!cart[variantId]) return;

    const qty = parseFloat(value);
    if (isNaN(qty) || qty <= 0) return;

    if (qty > cart[variantId].stock_qty) {
        alertaStock(cart[variantId].stock_qty);
        renderCart();
        return;
    }

    cart[variantId].quantity = qty;
    cart[variantId].subtotal = qty * cart[variantId].unit_price;
    renderCart();
}

// ===============================
// MODAL LOGIC
// ===============================
function abrirModalComprobante() {
    if (Object.keys(cart).length === 0) {
        Swal.fire('Atención', 'No hay productos en la venta', 'info');
        return;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModalComprobante() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
const modalFactura = document.getElementById('modalFactura');


const modalVuelto = document.getElementById('modalVuelto');
const pagaInput = document.getElementById('pagaInput');
const vueltoOutput = document.getElementById('vueltoOutput');
const totalCobro = document.getElementById('totalCobro');

// ===============================
// COBRO Y VUELTO
// ===============================
let totalActual = 0;

function abrirModalVuelto() {

    if (Object.keys(cart).length === 0) {
        Swal.fire('Atención', 'No hay productos en la venta', 'info');
        return;
    }

    totalGs = Object.values(cart)
        .reduce((sum, i) => sum + i.subtotal, 0);

    const currency = paymentCurrencyEl.value;

    if (currency === 'USD') {
        if (!rateUSD || rateUSD <= 0) {
            Swal.fire('Error', 'Tipo de cambio USD no definido', 'error');
            return;
        }
        totalCobroMoneda = +(totalGs / rateUSD).toFixed(2);
        simboloMoneda = '$';
    }
    else if (currency === 'BRL') {
        if (!rateBRL || rateBRL <= 0) {
            Swal.fire('Error', 'Tipo de cambio BRL no definido', 'error');
            return;
        }
        totalCobroMoneda = +(totalGs / rateBRL).toFixed(2);
        simboloMoneda = 'R$';
    }
    else {
        totalCobroMoneda = totalGs;
        simboloMoneda = '₲';
    }

    totalCobro.textContent = `${simboloMoneda} ${money(totalCobroMoneda, currency === 'PYG' ? 0 : 2)}`;

    pagaInput.value = '';
    vueltoOutput.textContent = `${simboloMoneda} 0`;

    modalVuelto.classList.remove('hidden');
    modalVuelto.classList.add('flex');

    setTimeout(() => pagaInput.focus(), 200);
}

function cerrarModalVuelto() {
    pagaInput.value = '';
    vueltoOutput.textContent = '₲ 0';

    modalVuelto.classList.add('hidden');
    modalVuelto.classList.remove('flex');
}


function calcularVuelto() {
    const paga = parseFloat(pagaInput.value.replace(',', '.')) || 0;
    const vuelto = paga - totalCobroMoneda;

    vueltoOutput.textContent =
        `${simboloMoneda} ${money(Math.max(vuelto, 0), simboloMoneda === '₲' ? 0 : 2)}`;
}


function confirmarCobro() {
    const paga = Number(pagaInput.value || 0);

    if (paga < totalActual) {
        Swal.fire('Atención', 'El monto es insuficiente', 'warning');
        return;
    }

    // ✅ ENVIAR MONTO PAGADO AL BACKEND
    document.getElementById('paid_amount').value = paga;

    cerrarModalVuelto();
    abrirModalComprobante();
}


// ===============================
// COMPROBANTE / FACTURA
// ===============================
function continuarVenta() {
    const tipo = document.querySelector('input[name="tipo"]:checked').value;
    document.getElementById('tipo_comprobante').value = tipo;

    cerrarModalComprobante();

    if (tipo === 'factura') {
        modalFactura.classList.remove('hidden');
        modalFactura.classList.add('flex');
        return;
    }

    document.getElementById('saleForm').submit();
}

function cerrarModalFactura() {
    modalFactura.classList.add('hidden');
    modalFactura.classList.remove('flex');
}

function confirmarFactura() {
    const ruc = document.getElementById('factura_ruc_modal').value.trim();
    const nombre = document.getElementById('factura_nombre').value.trim();
    const direccion = document.getElementById('factura_direccion').value.trim();

    if (!ruc || !nombre || !direccion) {
        Swal.fire('Atención', 'Completá todos los datos de la factura', 'warning');
        return;
    }

    document.getElementById('factura_ruc_input').value = ruc;
    document.getElementById('factura_nombre_input').value = nombre;
    document.getElementById('factura_direccion_input').value = direccion;

    cerrarModalFactura();
    document.getElementById('saleForm').submit();
}

// ===============================
// BUSCAR CLIENTE POR RUC
// ===============================
async function buscarClientePorRuc() {
    const ruc = document.getElementById('factura_ruc_modal')?.value.trim();
    if (!ruc) return;

    try {
        const res = await fetch(`/clients/by-ruc/${ruc}`);
        if (!res.ok) return;

        const cliente = await res.json();

        if (cliente) {
            document.getElementById('factura_nombre').value = cliente.nombre;
            document.getElementById('factura_direccion').value = cliente.direccion;

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Cliente encontrado',
                showConfirmButton: false,
                timer: 1200
            });
        }
    } catch (e) {}
}

</script>
@if(session('print_ticket'))
<script>
window.addEventListener('load', () => {
    const url = "{{ url('/sales') }}/{{ session('print_ticket') }}/receipt";

    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;

    document.body.appendChild(iframe);

    iframe.onload = () => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();

        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 1000);
    };
});
</script>
@endif

@endsection
