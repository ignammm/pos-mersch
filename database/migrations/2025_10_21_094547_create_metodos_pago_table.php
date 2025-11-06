<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->enum('tipo', [
                'efectivo', 
                'tarjeta_credito', 
                'tarjeta_debito', 
                'transferencia', 
                'cheque', 
                'cuenta_corriente',
                'otros'
            ])->default('otros');
            $table->decimal('comision_porcentaje', 5, 2)->default(0);
            $table->decimal('comision_fija', 10, 2)->default(0);
            $table->integer('dias_liquidacion')->default(0);
            $table->boolean('activo')->default(true);
            $table->text('configuracion')->nullable(); // Para configuraciones específicas
            $table->timestamps();
            
            // Índices
            $table->index('tipo');
            $table->index('activo');
            $table->index(['activo', 'tipo']);
        });
        
        // Insertar métodos de pago básicos
        $this->insertarMetodosBasicos();
    }

    private function insertarMetodosBasicos()
    {
        DB::table('metodos_pago')->insert([
            [
                'nombre' => 'Efectivo',
                'tipo' => 'efectivo',
                'comision_porcentaje' => 0,
                'comision_fija' => 0,
                'dias_liquidacion' => 0,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Tarjeta de Crédito',
                'tipo' => 'tarjeta_credito',
                'comision_porcentaje' => 15,
                'comision_fija' => 0,
                'dias_liquidacion' => 20,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Tarjeta de Débito',
                'tipo' => 'tarjeta_debito',
                'comision_porcentaje' => 15,
                'comision_fija' => 0,
                'dias_liquidacion' => 20,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'tipo' => 'transferencia',
                'comision_porcentaje' => 0,
                'comision_fija' => 0,
                'dias_liquidacion' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cuenta Corriente',
                'tipo' => 'cuenta_corriente',
                'comision_porcentaje' => 0,
                'comision_fija' => 0,
                'dias_liquidacion' => 30,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodos_pago');
    }
};
