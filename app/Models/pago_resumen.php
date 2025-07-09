<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pago_resumen extends Model
{
    protected $table = 'pago_resumen';
    protected $fillable = ['usuario', 'cobrador', 'servicio', 'codigo', 'cliente', 'cedula', 'direccion', 'pago', 'corte', 'plan', 'concepto', 'bolivares', 'pagomovil', 'referencia', 'fecha_pago_movil', 'dolares', 'euros', 'zelle_a', 'zelle_b', 'tasa', 'total', 'active', 'banco', 'telefono', 'id_cliente', 'tipo', 'banco_receptor', 'enlace'];
    protected $guarded = ['id'];

    public $timestamps = false;
}
