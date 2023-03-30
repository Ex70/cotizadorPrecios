<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\fmd;
use App\Models\Subcategoria;
use Illuminate\Http\Request;

class WPController extends Controller
{
    public function pruebas(){
        set_time_limit(0);
        $data['productos'] = Producto::Join('margenes', function ($margenes) {
            $margenes->on('productos.categoria_id', '=', 'margenes.categoria_id')
                ->on('productos.subcategoria_id', '=', 'margenes.subcategoria_id')
                ->on('productos.marca_id', '=', 'margenes.marca_id');
            })
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                ->where('productos.clave_ct', '=', 'MEMDAT6370')
                ->groupBy('productos.clave_ct')
                ->get(
                    [
                    'productos.clave_ct',
                    'productos.nombre',
                    'productos.descripcion_corta',
                    'categorias.nombre as categoria',
                    'subcategorias.nombre as subcategoria',
                    'marcas.nombre as marca',
                    'productos.precio_unitario',
                    'productos.enlace',
                    'productos.imagen',
                    'productos.existencias',
                    'productos.precio_unitario',
                    'existencias.almacen_id as almacen',
                    'existencias.existencias as existencias',
                    'margenes.margen_utilidad as margen'
                ]
            );
        return view('wp.productos', compact('data'));
    }

}