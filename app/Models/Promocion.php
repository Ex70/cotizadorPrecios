<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';
    protected $fillable = [
        'clave_ct',
        'descuento',
        'consulta',
        'fecha_inicio',
        'fecha_fin'
    ];
    // protected $dateFormat = 'c';
    public $timestamps = true;
}
