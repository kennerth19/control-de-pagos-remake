<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class estados extends Model
{
    protected $table = 'estados';
    protected $fillable = ['estado','color'];
    protected $guarded = ['id'];
}
