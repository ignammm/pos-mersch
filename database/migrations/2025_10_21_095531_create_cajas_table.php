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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->datetime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('monto_inicial', 12, 2);
            $table->decimal('monto_final_esperado', 12, 2)->nullable();
            $table->decimal('monto_final_real', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->enum('estado', ['abierta', 'cerrada', 'bloqueada'])->default('abierta');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'estado']);
            $table->index('fecha_apertura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
