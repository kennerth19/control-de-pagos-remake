<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class zona extends Model
{
    protected $table = 'zona';
    protected $fillable = ['zona'];
    protected $guarded = ['id'];  
}