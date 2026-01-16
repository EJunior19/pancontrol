<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supply_id')
                  ->constrained('supplies')
                  ->cascadeOnDelete();

            // in = entrada | out = salida
            $table->enum('type', ['in', 'out']);

            $table->decimal('quantity', 14, 3);

            // compra, producción, ajuste, devolución, etc.
            $table->string('reason', 100);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_movements');
    }
};
