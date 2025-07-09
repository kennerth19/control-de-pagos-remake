<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class acosador_log extends Model
{
    protected $table = 'acosador_log';
    protected $fillable = ['ip', 'resultado', 'categoria'];
    protected $guarded = ['id'];

    public $timestamps = false;
}
