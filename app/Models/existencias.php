<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class existencias extends Model
{
    protected $table = 'existencias';
    protected $fillable = ['categoria_id', 'serial', 'mac', 'tipo', 'asignado', 'observacion'];
    protected $guarded = ['id'];

    public $timestamps = false;
}