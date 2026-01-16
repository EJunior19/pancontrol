<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\ProductVariant;
use App\Models\DailyProduction;
use App\Models\CashRegister;

class DashboardController extends Controller
{
    public function index()
    {
        // Ventas del día
        $todaySales = Sale::whereDate('sale_date', today())
            ->sum('total_amount');

        // Productos activos
        $productsCount = ProductVariant::where('active', true)->count();

        // Producción del día  ✅ ESTA VARIABLE ES CLAVE
        $todayProduction = DailyProduction::whereDate('production_date', today())
            ->sum('produced_quantity');

        // Estado de caja
        $cashStatus = CashRegister::where('status', 'open')->exists()
            ? 'ABIERTA'
            : 'CERRADA';

        return view('dashboard.index', compact(
            'todaySales',
            'productsCount',
            'todayProduction',
            'cashStatus'
        ));
    }
}
