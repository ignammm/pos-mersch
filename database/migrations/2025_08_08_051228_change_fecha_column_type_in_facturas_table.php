<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFechaColumnTypeInFacturasTable extends Migration
{
    public function up()
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Cambiamos el tipo usando raw SQL por compatibilidad
            $table->dateTime('fecha')->change();
        });
    }

    public function down()
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Revertimos el cambio a date
            $table->date('fecha')->change();
        });
    }
}
