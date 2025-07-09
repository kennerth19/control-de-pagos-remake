<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class evento_log extends Model
{
    protected $table = 'evento_log';
    protected $fillable = ['eliminado_por', 'evento', 'fecha', 'bolivares', 'pagomovil', 'dolares', 'zelle_a', 'zelle_b', 'euro'];
    protected $guarded = ['id'];

    public $timestamps = false;
}
