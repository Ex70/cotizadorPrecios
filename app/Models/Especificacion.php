<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especificacion extends Model
{
    use HasFactory;

    protected $table = 'especificaciones';
    public $timestamps = true;

    protected $fillable = [
        'atributo_id',
        'valor',
        'clave_ct'
    ];

    public function atributo(){
        return $this->belongsTo(Atributo::class);
    }
}
