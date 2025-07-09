<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class caja extends Model
{
    protected $table = 'caja_fuerte';
    protected $fillable = ['usuario', 'evento', 'hora', 'bolivares', 'pagomovil', 'dolares', 'euros', 'enlace', 'receptor'];
    protected $guarded = ['id'];

    public $timestamps = false;
}