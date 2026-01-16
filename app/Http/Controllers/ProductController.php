<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Listado de productos
     */
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return view('products.index', compact('products'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Guardar producto + variante base automática
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:produced,resale',
            'unit'        => 'required|in:kg,unit',
            'active'      => 'nullable|boolean',

            // UI
            'has_variants' => 'nullable|boolean',

            // Informativo (en tu products actual existe price/price_per_kg; lo usamos como "referencia")
            'reference_price' => 'nullable|numeric|min:0',

            // Variante Única (solo si no tiene variantes)
            'barcode'   => 'nullable|string|max:50|unique:product_variants,barcode',
            'price'     => 'nullable|numeric|min:0',
            'cost'      => 'nullable|numeric|min:0',
            'stock_qty' => 'nullable|numeric|min:0',
        ]);

        $hasVariants = $request->boolean('has_variants');

        $product = null;

        DB::transaction(function () use ($request, $hasVariants, &$product) {

            $saleUnit     = $request->unit;              // 'kg' o 'unit'
            $allowDecimal = ($saleUnit === 'kg');

            // Precio "de referencia" solo informativo en products (porque tu tabla NO tiene reference_price)
            $ref = $request->filled('reference_price') ? (float)$request->reference_price : null;

            // 1) Crear producto base
            $product = Product::create([
                'name'          => $request->name,
                'type'          => $request->type,
                'sale_unit'     => $saleUnit,
                'allow_decimal' => $allowDecimal,
                'active'        => $request->boolean('active', true),

                // Guardamos como referencia en columns existentes (sin romper DB)
                'price'         => $saleUnit === 'unit' ? $ref : null,
                'price_per_kg'  => $saleUnit === 'kg' ? $ref : null,
                'stock_qty'     => 0,      // el stock real lo maneja la variante
                'barcode'       => null,   // el barcode real vive en la variante
            ]);

            // 2) Si NO tiene variantes: crear variante Única lista para vender
            if (!$hasVariants) {

                $price = $request->filled('price')
                    ? (float)$request->price
                    : ($ref ?? 0);

                ProductVariant::create([
                    'product_id'    => $product->id,
                    'name'          => 'Única',
                    'barcode'       => $request->filled('barcode') ? trim($request->barcode) : null,
                    'sale_unit'     => $saleUnit,

                    // precio según unidad
                    'price'         => $saleUnit === 'unit' ? $price : null,
                    'price_per_kg'  => $saleUnit === 'kg' ? $price : null,

                    // costo y stock
                    'cost'          => $request->filled('cost') ? (float)$request->cost : null,
                    'stock_qty'     => $request->filled('stock_qty') ? (float)$request->stock_qty : 0,

                    'allow_decimal' => $allowDecimal,
                    'active'        => true,
                ]);
            }
        });

        // 3) Redirigir según caso
        if ($hasVariants) {
            return redirect()
                ->route('product-variants.create', ['product_id' => $product->id])
                ->with('success', 'Producto creado. Ahora registrá sus variantes con el lector.');
        }

        return redirect()
            ->route('product-variants.index')
            ->with('success', 'Producto creado con variante Única. Ya podés vender con el lector.');
    }

    /**
     * Formulario de edición (solo datos del producto)
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Actualizar producto (SIN tocar precio, stock ni barcode)
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'active'      => 'nullable|boolean',
            'notes'       => 'nullable|string',
            'track_stock' => 'nullable|boolean',
        ]);

        $product->update([
            'name'        => $request->name,
            'notes'       => $request->notes,
            'track_stock' => $request->boolean('track_stock'),
            'active'      => $request->boolean('active'),
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado');
    }

    /**
     * Activar / desactivar producto + variantes
     */
    public function toggle(Product $product)
    {
        DB::transaction(function () use ($product) {

            $product->update([
                'active' => ! $product->active
            ]);

            // Desactivar / activar todas las variantes
            $product->variants()->update([
                'active' => $product->active
            ]);
        });

        return back();
    }
    public function lowStock()
    {
        $products = ProductVariant::where('stock_qty', '<=', 5)
            ->orderBy('stock_qty')
            ->get();

        return view('stock.low', compact('products'));
    }
}
