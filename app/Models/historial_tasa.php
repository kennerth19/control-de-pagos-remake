<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historial_tasa extends Model
{
    protected $table = 'historial_tasa';
    protected $fillable = ['tasa', 'fecha'];
    protected $guarded = ['id'];

    public $timestamps = false;
}