<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zegucom extends Model
{
    use HasFactory;

    protected $table = 'zegucom';

    protected $fillable = [
        'clave_ct',
        'sku',
        'precio_unitario'
    ];
}
