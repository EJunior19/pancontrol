<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    /**
     * Listado de insumos
     */
    public function index()
    {
        $supplies = Supply::orderBy('name')->get();
        return view('supplies.index', compact('supplies'));
    }

    /**
     * Formulario crear insumo
     */
    public function create()
    {
        return view('supplies.create');
    }

    /**
     * Guardar insumo
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100|unique:supplies,name',
            'unit'          => 'required|string|max:10',
            'minimum_stock' => 'nullable|numeric|min:0',
        ]);

        Supply::create([
            'name'          => $request->name,
            'unit'          => $request->unit,
            'minimum_stock' => $request->minimum_stock ?? 0,
            'stock'         => 0, // siempre empieza en 0
        ]);

        return redirect()
            ->route('supplies.index')
            ->with('success', 'Insumo creado correctamente');
    }
}
