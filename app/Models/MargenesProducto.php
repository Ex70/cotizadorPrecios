<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MargenesProducto extends Model
{
    use HasFactory;

    protected $table = 'margenes_por_producto';
    public $timestamps = true;

    protected $fillable = [
        'clave_ct',
        'margen_utilidad'
    ];
}
