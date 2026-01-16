<?php

namespace App\Providers;

use App\Models\CashRegister;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Evita romper comandos como migrate, seed, etc.
        if (app()->runningInConsole()) {
            return;
        }

        // ✅ Evita error si la tabla todavía no existe
        if (!Schema::hasTable('cash_registers')) {
            View::share('cajaAbierta', false);
            return;
        }

        View::share('cajaAbierta', CashRegister::where('status', 'open')->exists());
    }
}
