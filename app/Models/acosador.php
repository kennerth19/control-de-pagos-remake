<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class acosador extends Model
{
    protected $table = 'acosador';
    protected $fillable = ['ip', 'mac', 'comentario', 'servidor_id', 'servidor', 'status', 'disabled'];
    protected $guarded = ['id'];

    public $timestamps = false;
}
