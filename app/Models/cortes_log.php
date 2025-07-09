<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cortes_log extends Model
{
    protected $table = 'cortes_log';
    protected $fillable = ['evento','evento_tipo'];
    protected $guarded = ['id'];
}