<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class imagenProducto extends Model
{
    use HasFactory;

    protected $table = 'imagenes_productos';
    protected $fillable = [
        'clave_ct',
        'imagen',
        'ancho',
        'largo'
    ];
    // protected $dateFormat = 'c';
    public $timestamps = true;
}
