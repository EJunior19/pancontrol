<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            // Tipo de cambio definido al abrir caja (valores en Gs por 1 unidad)
            $table->decimal('rate_usd', 14, 4)->nullable()->after('opening_brl'); // ej: 7300.0000
            $table->decimal('rate_brl', 14, 4)->nullable()->after('rate_usd');    // ej: 1500.0000
        });
    }

    public function down(): void
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->dropColumn(['rate_usd', 'rate_brl']);
        });
    }
};
