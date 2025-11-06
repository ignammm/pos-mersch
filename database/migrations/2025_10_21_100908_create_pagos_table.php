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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade');
            $table->foreignId('metodo_pago_id')->constrained('metodos_pago')->onDelete('restrict');
            $table->foreignId('caja_id')->nullable('cajas')->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario que registró el pago
            
            // Datos del pago
            $table->decimal('monto', 12, 2);
            $table->decimal('comision', 10, 2)->default(0);
            $table->decimal('neto', 12, 2); // monto - comision
            
            // Información de estado
            $table->enum('estado', [
                'pendiente',
                'completado', 
                'fallido',
                'reversado',
                'reembolsado'
            ])->default('pendiente');
            
            // Fechas importantes
            $table->timestamp('fecha_pago');
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->timestamp('fecha_liquidacion')->nullable(); // Cuando el dinero llega a la cuenta
            
            // Información de transacción
            $table->string('referencia')->nullable(); // Número de transacción, voucher, etc.
            $table->string('codigo_autorizacion')->nullable(); // Código de autorización
            $table->string('comprobante')->nullable(); // Path al comprobante físico/PDF
            
            // Datos específicos por tipo de pago
            $table->json('datos_tarjeta')->nullable(); // Para tarjetas: últimos 4 dígitos, etc.
            $table->string('entidad_bancaria')->nullable(); // Para transferencias
            $table->string('numero_cheque')->nullable(); // Para cheques
            $table->date('fecha_vencimiento_cheque')->nullable(); // Para cheques
            
            // Auditoría
            $table->text('observaciones')->nullable();
            $table->text('notas_internas')->nullable(); // Para el personal interno
            
            $table->timestamps();
            
            // Índices para optimización
            $table->index('factura_id');
            $table->index('metodo_pago_id');
            $table->index('caja_id');
            $table->index('user_id');
            $table->index('estado');
            $table->index('fecha_pago');
            $table->index('fecha_liquidacion');
            $table->index(['estado', 'fecha_pago']);
            $table->index(['factura_id', 'estado']);
            
            // Índice único para referencia (si aplica)
            $table->unique(['referencia', 'metodo_pago_id'], 'pagos_referencia_metodo_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
