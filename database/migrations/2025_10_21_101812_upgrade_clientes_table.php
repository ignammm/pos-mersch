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
        // Agregar campos necesarios al cliente para cuentas corrientes
        Schema::table('clientes', function (Blueprint $table) {
            $table->decimal('limite_credito', 12, 2)->default(0)->after('cuit');
            $table->boolean('permite_cuenta_corriente')->default(false)->after('limite_credito');
            $table->enum('estado_crediticio', ['activo', 'suspendido', 'moroso'])->default('activo')->after('permite_cuenta_corriente');
            $table->date('fecha_ultima_revision_credito')->nullable()->after('estado_crediticio');
            $table->text('observaciones_credito')->nullable()->after('fecha_ultima_revision_credito');
            
            // Índices
            $table->index('permite_cuenta_corriente');
            $table->index('estado_crediticio');
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'limite_credito',
                'permite_cuenta_corriente',
                'estado_crediticio',
                'fecha_ultima_revision_credito',
                'observaciones_credito'
            ]);
        });
    }
};
