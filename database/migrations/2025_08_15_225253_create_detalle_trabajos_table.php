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
        Schema::create('detalle_trabajos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')->nullable()->constrained('trabajos')->onDelete('cascade');
            $table->unsignedBigInteger('articulo_id')->nullable();
            $table->string('observaciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_trabajos');
    }
};
