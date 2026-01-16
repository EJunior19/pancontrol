<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\CashRegister;
use App\Models\CashMovement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Pantalla de venta rápida
     */
    public function index()
    {
        $cashRegister = CashRegister::where('status', 'open')->first();

        if (! $cashRegister) {
            return redirect()
                ->route('cash.open.form')
                ->withErrors('Debe abrir la caja antes de vender.');
        }

        return view('sales.index', compact('cashRegister'));
    }

    /**
     * Registrar venta
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'items'            => 'required|string',
            'payment_method'   => 'required|in:efectivo,transferencia,qr',
            'tipo_comprobante' => 'required|in:ticket,factura',
            'payment_currency' => 'required|in:PYG,USD,BRL',
            'paid_amount'      => 'required|numeric|min:0',
        ]);

        $items = json_decode($data['items'], true);

        if (empty($items)) {
            return back()->withErrors('No hay productos en la venta.');
        }

        // 🔓 Caja abierta
        $cashRegister = CashRegister::where('status', 'open')->first();
        if (! $cashRegister) {
            return back()->withErrors('No hay una caja abierta.');
        }

        try {

            $sale = DB::transaction(function () use ($items, $data, $cashRegister) {

                /* ==========================
                 * 1️⃣ TOTAL DE LA VENTA (Gs)
                 * ========================== */
                $totalGs = collect($items)->sum(fn ($item) => $item['subtotal']);

                /* ==========================
                 * 2️⃣ MONEDA Y TIPO DE CAMBIO
                 * ========================== */
                $currency = $data['payment_currency'];
                $rate = 1;

                if ($currency === 'USD') {
                    $rate = (float) $cashRegister->rate_usd;
                    if ($rate <= 0) {
                        throw new \Exception('Definí el tipo de cambio USD al abrir caja.');
                    }
                }

                if ($currency === 'BRL') {
                    $rate = (float) $cashRegister->rate_brl;
                    if ($rate <= 0) {
                        throw new \Exception('Definí el tipo de cambio BRL al abrir caja.');
                    }
                }

                /* ==========================
                 * 3️⃣ TOTAL EN MONEDA DE PAGO
                 * ========================== */
                $totalInCurrency = ($currency === 'PYG')
                    ? $totalGs
                    : round($totalGs / $rate, 2);

                /* ==========================
                 * 4️⃣ MONTO PAGADO Y VUELTO
                 * ========================== */
                $paidAmount = (float) $data['paid_amount'];

                if ($paidAmount < $totalInCurrency) {
                    throw new \Exception('El monto pagado es insuficiente.');
                }

                $vuelto = round($paidAmount - $totalInCurrency, 2);

                /* ==========================
                 * 5️⃣ CREAR VENTA
                 * ========================== */
                $sale = Sale::create([
                    'sale_date'        => now(),
                    'total_amount'     => $totalGs,
                    'cash_register_id' => $cashRegister->id,
                    'payment_method'   => $data['payment_method'],
                    'status'           => 'completed',
                    'currency'         => 'PYG', // moneda base del sistema
                    'payment_currency' => $currency,
                    'exchange_rate'    => $rate,
                    'paid_amount'      => $paidAmount,
                    'tipo_comprobante' => $data['tipo_comprobante'],
                ]);

                /* ==========================
                 * 6️⃣ DETALLE + STOCK
                 * ========================== */
                foreach ($items as $item) {

                    $variant = ProductVariant::lockForUpdate()
                        ->findOrFail($item['variant_id']);

                    if ($variant->stock_qty < $item['quantity']) {
                        throw new \Exception("Stock insuficiente: {$variant->name}");
                    }

                    $sale->items()->create([
                        'product_variant_id' => $variant->id,
                        'quantity'           => $item['quantity'],
                        'unit_price'         => $item['unit_price'],
                        'subtotal'           => $item['subtotal'],
                    ]);

                    $variant->decrement('stock_qty', $item['quantity']);
                }

                /* ==========================
                 * 7️⃣ INGRESO EN CAJA (VENTA)
                 * ========================== */
                CashMovement::create([
                    'cash_register_id' => $cashRegister->id,
                    'type'             => 'in',
                    'currency'         => $currency,
                    'amount'           => $totalInCurrency,
                    'description'      => 'Venta #' . $sale->id .
                        ' (Total ₲ ' . number_format($totalGs, 0, ',', '.') . ')',
                ]);

                /* ==========================
                 * 8️⃣ EGRESO POR VUELTO (SI HAY)
                 * ========================== */
                if ($vuelto > 0) {
                    CashMovement::create([
                        'cash_register_id' => $cashRegister->id,
                        'type'             => 'out',
                        'currency'         => $currency,
                        'amount'           => $vuelto,
                        'description'      => 'Vuelto venta #' . $sale->id,
                    ]);
                }

                return $sale;
            });

        } catch (\Throwable $e) {
            return back()->withErrors($e->getMessage());
        }

        /* ==========================
         * 9️⃣ REDIRECCIÓN FINAL
         * ========================== */
        if ($data['tipo_comprobante'] === 'factura') {
            return redirect()->route('sales.invoice', $sale);
        }

        return redirect()
            ->route('sales.index')
            ->with('print_ticket', $sale->id);
    }

    /**
     * Buscar producto por código
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $variant = ProductVariant::where('barcode', $request->barcode)
            ->where('active', true)
            ->first();

        if (! $variant) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'id'            => $variant->id,
            'name'          => $variant->name,
            'sale_unit'     => $variant->sale_unit,
            'allow_decimal' => (bool) $variant->allow_decimal,
            'price'         => (float) $variant->price,
            'price_per_kg'  => (float) $variant->price_per_kg,
            'stock_qty'     => (float) $variant->stock_qty,
        ]);
    }

    /**
     * Recibo imprimible
     */
    public function receipt(Sale $sale)
    {
        $sale->load('items.variant');
        return view('sales.receipt', compact('sale'));
    }

    public function invoiceTest(Sale $sale)
    {
        return view('sales.invoice-test', compact('sale'));
    }

    /**
     * 🧾 Historial de ventas
     */
    public function history()
    {
        $sales = Sale::with('items.product')
            ->orderByDesc('sale_date')
            ->paginate(20);

        return view('sales.history', compact('sales'));
    }
}
