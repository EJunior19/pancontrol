<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CashRegisterController extends Controller
{
    /**
     * 📂 Vista principal de caja
     */
    public function index()
    {
        $cashRegister = CashRegister::where('status', 'open')->first();

        return view('cash.index', compact('cashRegister'));
    }

    /**
     * 🔓 Formulario de apertura de caja
     */
    public function openForm()
    {
        $cashRegister = CashRegister::where('status', 'open')->first();

        if ($cashRegister) {
            return redirect()
                ->route('cash.summary')
                ->with('info', 'La caja ya se encuentra abierta.');
        }

        return view('cash.open');
    }

    /**
     * 🔓 Abrir caja
     */
    public function open(Request $request)
    {
        $request->validate([
            'opening_gs'  => 'required|numeric|min:0',
            'opening_usd' => 'required|numeric|min:0',
            'opening_brl' => 'required|numeric|min:0',
            'rate_usd'    => 'required|numeric|min:1',
            'rate_brl'    => 'required|numeric|min:1',
        ]);

        if (CashRegister::where('status', 'open')->exists()) {
            return back()->withErrors('Ya existe una caja abierta.');
        }

        CashRegister::create([
            'opened_at'   => now(),
            'status'      => 'open',
            'opening_gs'  => $request->opening_gs,
            'opening_usd' => $request->opening_usd,
            'opening_brl' => $request->opening_brl,
            'rate_usd'    => $request->rate_usd,
            'rate_brl'    => $request->rate_brl,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Caja abierta correctamente.');
    }

    /**
     * 🔒 Formulario de cierre de caja
     */
    public function closeForm()
    {
        $cashRegister = CashRegister::where('status', 'open')->first();

        if (! $cashRegister) {
            return redirect()
                ->route('cash.open.form')
                ->withErrors('No hay una caja abierta.');
        }

        $totals = $this->getTotals($cashRegister->id);

        $in  = fn($c) => (float) ($totals[$c]->total_in  ?? 0);
        $out = fn($c) => (float) ($totals[$c]->total_out ?? 0);

        $expected = [
            'PYG' => $cashRegister->opening_gs  + $in('PYG') - $out('PYG'),
            'USD' => $cashRegister->opening_usd + $in('USD') - $out('USD'),
            'BRL' => $cashRegister->opening_brl + $in('BRL') - $out('BRL'),
        ];

        return view('cash.close', compact('cashRegister', 'expected'));
    }

    /**
     * 🔒 Cerrar caja
     */
    public function close(Request $request)
    {
        $request->validate([
            'closing_gs'  => 'required|numeric|min:0',
            'closing_usd' => 'required|numeric|min:0',
            'closing_brl' => 'required|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);

        $cashRegister = CashRegister::where('status', 'open')->firstOrFail();

        DB::transaction(function () use ($request, $cashRegister) {

            $totals = $this->getTotals($cashRegister->id);

            $in  = fn($c) => (float) ($totals[$c]->total_in  ?? 0);
            $out = fn($c) => (float) ($totals[$c]->total_out ?? 0);

            $expected_gs  = $cashRegister->opening_gs  + $in('PYG') - $out('PYG');
            $expected_usd = $cashRegister->opening_usd + $in('USD') - $out('USD');
            $expected_brl = $cashRegister->opening_brl + $in('BRL') - $out('BRL');

            $cashRegister->update([
                'closed_at'      => now(),
                'status'         => 'closed',
                'closing_gs'     => $request->closing_gs,
                'closing_usd'    => $request->closing_usd,
                'closing_brl'    => $request->closing_brl,
                'difference_gs'  => $request->closing_gs  - $expected_gs,
                'difference_usd' => $request->closing_usd - $expected_usd,
                'difference_brl' => $request->closing_brl - $expected_brl,
                'notes'          => $request->notes,
            ]);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Caja cerrada correctamente.');
    }

    /**
     * 💰 Resumen del día
     */
    public function summary()
    {
        $cashRegister = CashRegister::where('status', 'open')->first();

        if (! $cashRegister) {
            return redirect()
                ->route('cash.open.form')
                ->withErrors('No hay una caja abierta.');
        }

        $movements = DB::table('cash_movements')
            ->where('cash_register_id', $cashRegister->id)
            ->orderByDesc('created_at')
            ->get();

        $totals = $this->getTotals($cashRegister->id);

        return view('cash.summary', compact(
            'cashRegister',
            'movements',
            'totals'
        ));
    }

    /**
     * 📜 Historial de cajas cerradas
     */
    public function history()
    {
        $registers = CashRegister::where('status', 'closed')
            ->orderByDesc('closed_at')
            ->paginate(20);

        return view('cash.history', compact('registers'));
    }

    /**
     * 📄 PDF de arqueo de caja (descarga directa, sin recargar ni abrir pestaña)
     */
    public function pdf($id)
    {
        // 🔒 Solo cajas cerradas
        $cashRegister = CashRegister::where('id', $id)
            ->where('status', 'closed')
            ->firstOrFail();

        // 📋 Detalle completo de movimientos
        $movements = DB::table('cash_movements')
            ->where('cash_register_id', $cashRegister->id)
            ->orderBy('created_at')
            ->get();

        // 📊 Resumen por moneda (rápida detección)
        $summaryByCurrency = DB::table('cash_movements')
            ->selectRaw("
                currency,
                COUNT(*) AS movimientos,
                SUM(CASE WHEN type = 'in'  THEN amount ELSE 0 END) AS ingresos,
                SUM(CASE WHEN type = 'out' THEN amount ELSE 0 END) AS egresos,
                SUM(CASE WHEN type = 'in'  THEN amount ELSE -amount END) AS saldo
            ")
            ->where('cash_register_id', $cashRegister->id)
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        // 🧮 Totales clásicos
        $totals = $this->getTotals($cashRegister->id);

        // 📄 Generar PDF (A4)
        $pdf = Pdf::loadView(
            'cash.report-pdf',
            compact(
                'cashRegister',
                'movements',
                'summaryByCurrency',
                'totals'
            )
        );

        // ⬇️ Descarga directa (sin pestaña, sin recarga)
        return $pdf->download('arqueo-caja-' . $cashRegister->id . '.pdf');
    }

    /**
     * 🔁 Totales por moneda (IN / OUT)
     */
    private function getTotals(int $cashRegisterId)
    {
        return DB::table('cash_movements')
            ->selectRaw("
                currency,
                SUM(CASE WHEN type = 'in'  THEN amount ELSE 0 END) AS total_in,
                SUM(CASE WHEN type = 'out' THEN amount ELSE 0 END) AS total_out
            ")
            ->where('cash_register_id', $cashRegisterId)
            ->groupBy('currency')
            ->get()
            ->keyBy('currency');
    }
}
