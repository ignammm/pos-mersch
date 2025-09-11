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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();


            // Número único de presupuesto (puede contener prefijos si querés)
            $table->string('numero')->unique();


            // FK a clientes y usuario que creó el presupuesto
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();


            // Fechas
            $table->datetime('fecha_emision')->nullable();
            $table->datetime('fecha_validez')->nullable();


            // Estado del presupuesto
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado', 'convertido'])->default('pendiente');


            // Totales y desglose (ajustá precisión si necesitás otra)
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->nullable();
            $table->decimal('iva', 12, 2)->nullable();
            $table->decimal('total_estimado', 12, 2)->default(0);
            $table->decimal('total_final', 12, 2)->nullable();


            // Observaciones libres
            $table->text('observaciones')->nullable();


            // Polimórfica: a qué se convirtió (Venta o Trabajo)
            // Usamos los mismos nombres que en los modelos que definiste:
            // tipo_conversion (string) + conversion_id (unsignedBigInteger)
            $table->string('tipo_conversion')->nullable();
            $table->unsignedBigInteger('conversion_id')->nullable();
            $table->index(['tipo_conversion', 'conversion_id']);

            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
