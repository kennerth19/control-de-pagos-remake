<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventario_log extends Model
{
    protected $table = 'inventario_log';
    protected $fillable = ['usuario', 'evento', 'tipo', 'fecha'];
    protected $guarded = ['id'];

    public $timestamps = false;
}
