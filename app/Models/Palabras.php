<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Palabras extends Model
{
    use HasFactory;
    protected $table = 'palabras_clave';
    protected $fillable = [
        'clave_ct',
        'palabra'
    ];
    public $timestamps = true;
}
