<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class caja_log extends Model
{
    protected $table = 'caja_log';
    protected $fillable = ['eliminado_por', 'evento', 'fecha', 'bolivares', 'pagomovil', 'dolares', 'zelle_a', 'zelle_b', 'euro'];
    protected $guarded = ['id'];

    public $timestamps = false;
}
