<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\DailyProduction;
use App\Models\Supply;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductionController extends Controller
{
    /**
     * 📊 Panel principal de Producción Diaria
     */
    public function index()
    {
        $today = Carbon::today();

        $totalProduced = DailyProduction::whereDate('production_date', $today)
            ->sum('produced_quantity');

        $totalWaste = DailyProduction::whereDate('production_date', $today)
            ->sum('waste_quantity');

        return view('production.index', [
            'totalProduced' => $totalProduced,
            'totalWaste'    => $totalWaste,
        ]);
    }

    /**
     * 📝 Formulario para registrar producción
     */
    public function create()
    {
        // Productos base (Pan, Yerba, etc.)
        $products = Product::where('active', true)
            ->orderBy('name')
            ->get();

        // Variantes agrupadas por producto (para select dinámico)
        $variantsGroupedByProduct = ProductVariant::select(
                'id',
                'product_id',
                'name',
            )
            ->orderBy('name')
            ->get()
            ->groupBy('product_id');

        // Insumos
        $supplies = Supply::orderBy('name')->get();

        return view('production.create', compact(
            'products',
            'variantsGroupedByProduct',
            'supplies'
        ));
    }

    /**
     * 💾 Guardar producción del día + consumo de insumos
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_variant_id'         => 'required|exists:product_variants,id',
            'produced_quantity'          => 'required|numeric|min:0.001',
            'waste_quantity'             => 'nullable|numeric|min:0',

            'supplies'                   => 'required|array|min:1',
            'supplies.*.supply_id'       => 'required|exists:supplies,id',
            'supplies.*.quantity_used'   => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($request) {

            $today = Carbon::today();

        // ✅ Mantener decimales (Kg)
        $producedQty = (float) $request->produced_quantity;
        $wasteQty    = (float) ($request->waste_quantity ?? 0);

        if ($wasteQty > $producedQty) {
            throw ValidationException::withMessages([
                'waste_quantity' => 'La merma no puede ser mayor a lo producido.',
            ]);
        }

        $netQty = $producedQty - $wasteQty;

        // 🔒 Bloqueo de variante
        $variant = ProductVariant::lockForUpdate()
            ->findOrFail($request->product_variant_id);

        // ✅ Sumás al stock real por producción
        $variant->increment('stock_qty', $netQty);

        // 🥖 PRODUCCIÓN DIARIA
        $production = DailyProduction::create([
            'production_date'     => $today,
            'product_variant_id'  => $variant->id,
            'produced_quantity'   => $producedQty,
            'waste_quantity'      => $wasteQty,
            'net_quantity'        => $netQty,
            'remaining_quantity'  => $netQty,
            'sold_quantity'       => 0,
        ]);


            // 🧂 INSUMOS
            foreach ($request->supplies as $item) {

                $supply = Supply::lockForUpdate()
                    ->findOrFail($item['supply_id']);

                $production->supplies()->create([
                    'supply_id'     => $supply->id,
                    'quantity_used' => $item['quantity_used'],
                    'unit'          => $supply->unit,
                ]);

                $supply->decrement('stock', $item['quantity_used']);
            }
        });

        return redirect()
            ->route('production.index')
            ->with('success', 'Producción registrada correctamente');
    }

    /**
     * 📜 Historial de producción
     */
    public function history()
    {
        $productions = DailyProduction::with([
                'productVariant',
                'supplies.supply'
            ])
            ->orderBy('production_date', 'desc')
            ->paginate(20);

        return view('production.history', compact('productions'));
    }

    /**
     * 🥖 Productos más vendidos (HOY)
     */
    public function topProducts()
    {
        $today = now()->toDateString();

        $products = DB::table('sale_items as si')
            ->join('sales as s', 's.id', '=', 'si.sale_id')
            ->join('product_variants as pv', 'pv.id', '=', 'si.product_variant_id')
            ->whereDate('s.sale_date', $today)
            ->select(
                'pv.name',
                DB::raw('SUM(si.quantity) as cantidad_vendida'),
                DB::raw('SUM(si.subtotal) as total_vendido')
            )
            ->groupBy('pv.name')
            ->orderByDesc('cantidad_vendida')
            ->get();

        return view('production.top-products', compact('products'));
    }

    /**
     * 📈 Histórico general de productos vendidos
     */
    public function productsHistory()
    {
        $rows = DB::table('daily_production as dp')
            ->join('product_variants as pv', 'pv.id', '=', 'dp.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->select(
                'dp.production_date',
                'p.name as product',     // 👈 alias correcto
                'pv.name as variant',    // 👈 alias correcto
                'dp.produced_quantity',
                'dp.waste_quantity',
                'dp.net_quantity'
            )
            ->orderByDesc('dp.production_date')
            ->get();

        return view('production.products-history', compact('rows'));
    }
}
