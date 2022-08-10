<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mipc extends Model
{
    use HasFactory;

    protected $table = 'mipc';

    protected $fillable = [
        'clave_ct',
        'sku',
        'precio_unitario'
    ];
}
