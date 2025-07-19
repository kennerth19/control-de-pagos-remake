<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientes extends Model
{
    protected $table = 'clientes';
    protected $fillable = ['nombre', 'direccion', 'cedula', 'direccion', 'correo', 'estado', 'plan_id', 'tlf', 'observacion', 'servidor', 'ip', 'dia', 'corte', 'dia_i', 'active', 'almacen', 'deuda', 'motivo_deuda', 'mac', 'servicio_id', 'principal', 'ticket', 'deuda_p', 'cambios', 'congelado', 'congelar', 'prorroga_hasta', 'nota_prorroga', 'prorroga', 'ult_prorroga', 'dias_prorroga', 'tipo_cliente', 'iptv', 'mes', 'conducta', 'conducta_nota', 'sector_id', 'parche_prorroga', 'asignacion', 'telegramId'];
    protected $guarded = ['id'];

    public $timestamps = false;

    //constantes para la suspension del servicio
    const ESTADO_SUSPENDIDO = 5;
    const TIPO_NO_PREMIUM = 0;
    const PRORROGA_NO = 0;
}