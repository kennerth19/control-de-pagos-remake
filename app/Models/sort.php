<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sort extends Model
{
    protected $table = 'sort';
    protected $fillable = ['ip','tasa'];
    protected $guarded = ['id'];  

    public $timestamps = false;
}
