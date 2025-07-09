<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class planes extends Model
{
    protected $table = 'planes';
    protected $fillable = ['plan','valor','tipo'];
    protected $guarded = ['id'];  

    public $timestamps = false;
}
