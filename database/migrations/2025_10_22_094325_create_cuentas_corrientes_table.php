<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cuentas_corrientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->onDelete('set null');
            $table->foreignId('pago_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('tipo_movimiento', ['debe', 'haber']);
            $table->decimal('monto', 12, 2);
            $table->decimal('saldo_actual', 12, 2);
            $table->string('descripcion');
            $table->datetime('fecha_movimiento');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pendiente', 'pagado', 'vencido', 'cancelado'])->default('pendiente');
            $table->timestamps();
            
            $table->index(['cliente_id', 'estado']);
            $table->index('fecha_movimiento');
            $table->index('fecha_vencimiento');
            $table->index('tipo_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_corrientes');
    }
};
