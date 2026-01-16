<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\SupplyMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyMovementController extends Controller
{
    /**
     * Formulario de ingreso de insumo (entrada)
     */
    public function create()
    {
        $supplies = Supply::orderBy('name')->get();

        return view('supplies.movements.create', compact('supplies'));
    }

    /**
     * Guardar ingreso de insumo (ENTRADA DE STOCK)
     */
    public function store(Request $request)
    {
        $request->validate([
            'supply_id' => 'required|exists:supplies,id',
            'quantity'  => 'required|numeric|min:0.001',
            'reason'    => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {

            $supply = Supply::lockForUpdate()->findOrFail($request->supply_id);

            SupplyMovement::create([
                'supply_id' => $supply->id,
                'type'      => 'in',
                'quantity'  => $request->quantity,
                'reason'    => $request->reason ?? 'Ingreso manual',
            ]);

            $supply->increment('stock', $request->quantity);
        });

        return redirect()
            ->route('supplies.index')
            ->with('success', 'Insumo ingresado correctamente');
    }

    /**
     * Ajuste manual de stock (entrada / salida)
     */
    public function adjust(Request $request)
    {
        $request->validate([
            'supply_id' => 'required|exists:supplies,id',
            'type'      => 'required|in:in,out',
            'quantity'  => 'required|numeric|min:0.001',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $supply = Supply::lockForUpdate()->findOrFail($request->supply_id);

                if ($request->type === 'out' && $supply->stock < $request->quantity) {
                    throw new \Exception('Stock insuficiente para realizar la salida');
                }

                SupplyMovement::create([
                    'supply_id' => $supply->id,
                    'type'      => $request->type,
                    'quantity'  => $request->quantity,
                    'reason'    => 'Ajuste manual',
                ]);

                if ($request->type === 'in') {
                    $supply->increment('stock', $request->quantity);
                } else {
                    $supply->decrement('stock', $request->quantity);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }

        return redirect()
            ->route('supplies.index')
            ->with('success', 'Stock ajustado correctamente');
    }
}
