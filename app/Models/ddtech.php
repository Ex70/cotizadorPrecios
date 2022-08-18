<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ddtech extends Model
{
    use HasFactory;

    protected $table = 'ddtech';

    protected $fillable = [
        'clave_ct',
        'sku',
        'precio_unitario'
    ];
}
