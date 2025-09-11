<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_presupuestos', function (Blueprint $table) {
            $table->id();


            // FK al presupuesto
            $table->foreignId('presupuesto_id')->constrained('presupuestos')->cascadeOnDelete();


            // Artículo (nullable para permitir conceptos de mano de obra)
            $table->foreignId('articulo_id')->nullable()->constrained('articulos')->nullOnDelete();


            // Descripción libre si no hay artículo
            $table->text('descripcion')->nullable();


            // Cantidad y precios
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->boolean('activo')->default(true);

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_presupuesto');
    }
};
