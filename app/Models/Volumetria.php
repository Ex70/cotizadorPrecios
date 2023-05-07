<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volumetria extends Model
{
    use HasFactory;
    protected $table = 'volumetria';
    public $timestamps = true;

    protected $fillable = [
        'clave_ct','peso','largo','alto','ancho','upc','ean'
    ];
}
