<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();

            // Estado de caja
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status')->default('open'); // open | closed

            // Apertura
            $table->decimal('opening_gs', 14, 2)->default(0);
            $table->decimal('opening_usd', 14, 2)->default(0);
            $table->decimal('opening_brl', 14, 2)->default(0);

            // Cierre (conteo real)
            $table->decimal('closing_gs', 14, 2)->nullable();
            $table->decimal('closing_usd', 14, 2)->nullable();
            $table->decimal('closing_brl', 14, 2)->nullable();

            // Diferencias
            $table->decimal('difference_gs', 14, 2)->nullable();
            $table->decimal('difference_usd', 14, 2)->nullable();
            $table->decimal('difference_brl', 14, 2)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
