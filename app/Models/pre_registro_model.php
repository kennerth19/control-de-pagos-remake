<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pre_registro_model extends Model
{
    protected $table = 'pre-registro';
    protected $fillable = ['nombre', 'direccion', 'cedula', 'tipo_de_servicio', 'plan', 'fecha_de_pago', 'observacion', 'comentario', 'estado','pagado','cobrador','valor','total','instalacion', 'asignacion'];
    protected $guarded = ['id'];

    public $timestamps = false;
}