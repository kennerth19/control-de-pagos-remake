<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comprobacion extends Model
{
    protected $table = 'comprobacion';
    protected $fillable = ['documento', 'sistema', 'alerta'];
    protected $guarded = ['id'];

    public $timestamps = false;
}