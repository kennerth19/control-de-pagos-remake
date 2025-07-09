<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clientes_aux extends Model
{
    protected $table = 'clientes_aux';
    protected $fillable = ['full_name','id_user','dir','tlf','servidor', 'total'];
    protected $guarded = ['id'];
}
