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
        Schema::create('referencia_rsf', function (Blueprint $table) {
            $table->string('marca_rsf')->nullable();
            $table->string('articulo')->nullable();
            $table->string('fabrica')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('tipo_txt')->nullable();
            $table->string('marca_original')->nullable();
            $table->decimal('precio_lista', 10, 2)->nullable();
            $table->decimal('precio_neto', 10, 2)->nullable();
            $table->integer('stock_final')->nullable();
            $table->integer('modulo_venta')->nullable();
            $table->string('rubro')->nullable();
            $table->string('segmento')->nullable();
            $table->string('enlace')->nullable();
            $table->string('oem')->nullable();
            $table->string('codigo_barra')->nullable();
            $table->string('codigo_rsf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referencia_rsf');
    }
};
