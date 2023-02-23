<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Existencias extends Model
{
    use HasFactory;

    protected $table = 'existencias';
    public $timestamps = true;

    protected $fillable = [
        'clave_alm_api',
        'clave_alm_json',
        'clave_ct',
        'sucursal',
        'ciudad',
        'estado',
        'existencias',
    ];
}
