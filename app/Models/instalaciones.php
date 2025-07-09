<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class instalaciones extends Model
{
    protected $table = 'instalaciones';
    protected $fillable = ['router', 'valor', 'categoria', 'active', 'inventario_categoria'];
    protected $guarded = ['id'];

    public $timestamps = false;
}