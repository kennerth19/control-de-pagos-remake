<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('cedula');
            $table->string('direccion');
            $table->integer('estado');
            $table->integer('plan_id');
            $table->string('tlf');
            $table->string('observacion')->nullable();
            $table->string('servidor');
            $table->string('ip');
            $table->date('dia');
            $table->date('corte');
            $table->date('dia_i');
            $table->integer('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
