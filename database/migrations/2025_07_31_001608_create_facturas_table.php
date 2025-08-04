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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->date('fecha');
            $table->string('tipo_comprobante')->nullable();
            $table->string('numero')->nullable();
            $table->decimal('monto_original', 10, 2);
            $table->decimal('monto_final', 10, 2)->nullable();
            $table->decimal('descuento_aplicado', 5,2)->nullable();
            $table->string('forma_pago')->nullable();
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
