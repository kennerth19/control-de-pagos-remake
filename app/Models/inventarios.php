<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventarios extends Model
{
    protected $table = 'inventarios';
    protected $fillable = ['producto', 'tipo', 'ult_mod'];
    protected $guarded = ['id'];

    public $timestamps = false;
}