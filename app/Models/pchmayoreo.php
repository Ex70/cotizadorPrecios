<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pchmayoreo extends Model
{
    use HasFactory;

    protected $table = 'pchmayoreo';

    protected $fillable = [
        'clave_ct',
        'sku',
        'precio_unitario'
    ];
}
