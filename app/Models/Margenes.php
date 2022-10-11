<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Margenes extends Model
{
    protected $table = 'margenes';
    public $timestamps = true;

    protected $fillable = [
        'categoria_id',
        'subcategoria_id',
        'marca_id',
        'existencias',
        'margen_utilidad'
    ];
}
