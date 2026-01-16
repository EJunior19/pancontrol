<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Listado de variantes
     * Puede listar todas o filtrar por producto
     */
    public function index(Request $request)
    {
        $query = ProductVariant::with('product')
            ->orderBy('name');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $variants = $query->get();

        return view('product_variants.index', compact('variants'));
    }

    /**
     * Formulario de creación de variante
     * Puede venir con producto preseleccionado
     */
    public function create(Request $request)
    {
        $products = Product::where('active', true)
            ->orderBy('name')
            ->get();

        $productId = $request->get('product_id');

        return view('product_variants.create', compact('products', 'productId'));
    }

    /**
     * Formulario de edición
     */
    public function edit(ProductVariant $variant)
    {
        return view('product_variants.edit', compact('variant'));
    }

    /**
     * Guardar nueva variante
     */
    public function store(Request $request)
    {
        // ✅ Normalizar números con formato paraguayo
        // Ej: 7.500 → 7500
        $request->merge([
            'price'        => $request->filled('price')
                                    ? str_replace('.', '', $request->price)
                                    : null,

            'price_per_kg' => $request->filled('price_per_kg')
                                    ? str_replace('.', '', $request->price_per_kg)
                                    : null,

            'cost'         => $request->filled('cost')
                                    ? str_replace('.', '', $request->cost)
                                    : null,

            // Stock puede ser decimal (kg) o entero (unit)
            'stock_qty'    => $request->filled('stock_qty')
                                    ? str_replace(',', '.', str_replace('.', '', $request->stock_qty))
                                    : 0,
        ]);

        // ✅ Validación
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'barcode'      => 'nullable|string|max:50|unique:product_variants,barcode',
            'name'         => 'required|string|max:255',
            'sale_unit'    => 'required|in:unit,kg',
            'price'        => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'cost'         => 'nullable|numeric|min:0',
            'stock_qty'    => 'required|numeric|min:0',
        ]);

        $saleUnit = $request->sale_unit;

        // 🔒 Validación lógica según unidad de venta
        if ($saleUnit === 'unit' && !$request->filled('price')) {
            return back()
                ->withErrors('Para venta por unidad, cargá el precio por unidad.')
                ->withInput();
        }

        if ($saleUnit === 'kg' && !$request->filled('price_per_kg')) {
            return back()
                ->withErrors('Para venta por kilo, cargá el precio por kilo.')
                ->withInput();
        }

        // ✅ Crear variante
        ProductVariant::create([
            'product_id'    => $request->product_id,
            'barcode'       => $request->filled('barcode') ? trim($request->barcode) : null,
            'name'          => $request->name,
            'sale_unit'     => $saleUnit,
            'price'         => $saleUnit === 'unit' ? (float) $request->price : null,
            'price_per_kg'  => $saleUnit === 'kg' ? (float) $request->price_per_kg : null,
            'cost'          => $request->filled('cost') ? (float) $request->cost : null,
            'stock_qty'     => (float) $request->stock_qty,
            'allow_decimal' => $saleUnit === 'kg',
            'active'        => true,
        ]);

        return redirect()
            ->route('product-variants.create', ['product_id' => $request->product_id])
            ->with('success', 'Variante registrada. Podés cargar otra.');
    }

    /**
     * Actualizar variante existente
     */
    public function update(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'barcode'      => 'nullable|string|max:50|unique:product_variants,barcode,' . $variant->id,
            'price'        => 'nullable|numeric|min:0',
            'price_per_kg' => 'nullable|numeric|min:0',
            'stock_qty'    => 'required|numeric|min:0',
            'active'       => 'nullable|boolean',
        ]);

        // 🔒 Reglas de negocio según unidad
        if ($variant->sale_unit === 'kg' && empty($data['price_per_kg'])) {
            return back()
                ->withErrors('El precio por kilo es obligatorio.')
                ->withInput();
        }

        if ($variant->sale_unit === 'unit' && empty($data['price'])) {
            return back()
                ->withErrors('El precio por unidad es obligatorio.')
                ->withInput();
        }

        $variant->update([
            'name'         => $data['name'],
            'barcode'      => $data['barcode'] ?? null,
            'price'        => $variant->sale_unit === 'unit'
                                ? $data['price']
                                : null,
            'price_per_kg' => $variant->sale_unit === 'kg'
                                ? $data['price_per_kg']
                                : null,
            'active'       => $request->boolean('active'),
        ]);

        return redirect()
            ->route('product-variants.index', ['product_id' => $variant->product_id])
            ->with('success', 'Variante actualizada correctamente');
    }

}
