<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FMD extends Model
{
    use HasFactory;

    protected $table = 'productos';
    public $timestamps = true;

    protected $fillable = [
        'clave_ct',
        'subcategoria',
        'nombre',
        'imagen',
        'descripcion_corta',
        'created_at'
    ];
}
