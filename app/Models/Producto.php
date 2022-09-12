<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    public $timestamps = true;

    protected $fillable = [
        'clave_ct',
        'sku',
        'nombre',
        'ean',
        'upc',
        'imagen',
        'precio_unitario',
        'categoria_id',
        'subcategoria_id',
        'marca_id',
        'existencias',
        'estatus'
    ];
}
