<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_production_supplies', function (Blueprint $table) {
            $table->id();

            // Relación con producción
            $table->foreignId('daily_production_id')
                  ->constrained('daily_production')
                  ->cascadeOnDelete();

            // Relación con insumo
            $table->foreignId('supply_id')
                  ->constrained('supplies')
                  ->cascadeOnDelete();

            // Cantidad usada
            $table->decimal('quantity_used', 14, 3);

            // Unidad (kg, g, l, u, etc)
            $table->string('unit', 10);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_production_supplies');
    }
};
