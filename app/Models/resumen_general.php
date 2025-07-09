<?php

namespace App\Models;

use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class resumen_general extends Model
{
    protected $table = 'resumen_general';
    protected $fillable = ['descripcion', 'tipo', 'fecha','id_cliente', 'usuario'];
    protected $guarded = ['id'];

    public $timestamps = false;
}