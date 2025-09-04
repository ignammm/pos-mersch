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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('articulo_id')->constrained();
            $table->integer('cantidad');
            $table->enum('tipo', ['entrada', 'salida', 'ajuste']);
            $table->string('motivo', 50); // Limitar longitud
            $table->unsignedBigInteger('movimiento_origen_id');
            $table->string('movimiento_origen_type', 50); // Limitar longitud
            $table->enum('estado_reposicion', ['pendiente', 'reintegrado', 'no_aplica'])->default('no_aplica');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índice manual con nombre corto
            $table->index(
                ['movimiento_origen_type', 'movimiento_origen_id'],
                'mov_inv_origin_index' // ← Nombre corto personalizado
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
