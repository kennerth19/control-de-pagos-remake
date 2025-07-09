<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class servidores extends Model
{
    protected $table = 'servidores';
    protected $fillable = ['nombre_de_servidor','ip','puerto', 'active'];
    protected $guarded = ['id'];  

    public $timestamps = false;
}
