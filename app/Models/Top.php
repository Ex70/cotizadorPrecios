<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Top extends Model
{
    use HasFactory;

    protected $table = 'tops_mensuales';
    public $timestamps = true;

    protected $fillable = [
        'clave_ct',
        'marca',
        'mes',
        'anio'
    ];
}
