<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_resumen', function (Blueprint $table) {
            $table->id();
            $table->string('usuario');
            $table->string('cobrador');
            $table->string('codigo');
            $table->string('servicio');
            $table->string('cliente');
            $table->string('cedula');
            $table->string('direccion');
            $table->date('pago');
            $table->date('corte');
            $table->string('plan');
            $table->string('concepto');
            $table->double('bolivares', 8, 2);
            $table->double('pagomovil', 8, 2);
            $table->string('referencia');
            $table->double('dolares', 8, 2);
            $table->double('euros', 8, 2);
            $table->double('zelle_a', 8, 2);
            $table->double('zelle_b', 8, 2);
            $table->double('tasa', 8, 2);
            $table->double('total', 8, 2);
            $table->integer('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_resumen');
    }
};
