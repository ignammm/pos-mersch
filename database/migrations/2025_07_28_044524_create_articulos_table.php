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
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo_interno')->unique();
            $table->string('codigo_proveedor')->nullable();
            $table->string('marca');
            $table->text('descripcion')->nullable();
            $table->string('unidad')->default('unidad'); 
            $table->foreignId('proveedor_id')
              ->nullable()
              ->constrained('proveedores')
              ->nullOnDelete(); // o 'cascade' según tu lógica
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn('proveedor_id');
        });
    }
};
