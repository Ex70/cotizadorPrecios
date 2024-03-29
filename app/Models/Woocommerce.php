<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Woocommerce extends Model
{
    use HasFactory;
    protected $table = 'woocommerce';
    public $timestamps = true;

    protected $fillable = [
        'idWP','clave_ct','precio_venta','precio_venta_rebajado','fecha_inicio','fecha_fin'
    ];
}
