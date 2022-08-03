<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class totalesDecme extends Model
{
    use HasFactory;
    protected $table = "totales_decme";

    protected $fillable = [
        'title',
        'sku',
        'precio'
    ];
    // public $timestamps = false;
}
