<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $table = 'atributos';
    public $timestamps = true;

    protected $fillable = [
        'nombre'
    ];

    public function especificaciones(){
        return $this->hasMany(Especificacion::class);
    }
}
