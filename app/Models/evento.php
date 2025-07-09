<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class evento extends Model
{
    protected $table = 'evento_diario';
    protected $fillable = ['usuario', 'evento', 'hora', 'bolivares', 'pagomovil', 'dolares', 'euros', 'enlace', 'receptor'];
    protected $guarded = ['id'];

    public $timestamps = false;
}
