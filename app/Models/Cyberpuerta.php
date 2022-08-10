<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cyberpuerta extends Model
{
    use HasFactory;

    protected $table = 'cyberpuerta';

    protected $fillable = [
        'clave_ct',
        'sku',
        'precio_unitario'
    ];
}
