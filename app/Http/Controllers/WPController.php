<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use App\Models\fmd;
use App\Models\Subcategoria;
use Illuminate\Http\Request;

class WPController extends Controller
{
    public function wpxalapa(){
        set_time_limit(0);
        $data['productos'] = Producto::Join('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 15)
                ->where('existencias.existencias', '>', 0)
                ->get(
                    [
                    'productos.clave_ct',
                    'productos.nombre',
                    'productos.descripcion_corta',
                    'categorias.nombre as categoria',
                    'subcategorias.nombre as subcategoria',
                    'productos.precio_unitario',
                    'productos.enlace',
                    'productos.imagen',
                    'productos.existencias',
                    'productos.precio_unitario',
                    'existencias.almacen_id as almacen',
                    'existencias.existencias as existencias',
                    'margenes_por_producto.margen_utilidad as margen'
                ]
            );
        return view('wp.productosXalapa', compact('data'));
    }

    public function pruebas(){
        set_time_limit(0);
        $data['productos'] = Producto::Join('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->where('productos.estatus', 'Activo')
                ->where('productos.existencias', '>', 0)
                ->whereNotIn('productos.clave_ct',  Producto::join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                    ->where('productos.estatus', 'Activo')
                    ->where('existencias.almacen_id', '=', 15)
                    ->where('existencias.existencias', '>', 0)
                    ->get(
                        'productos.clave_ct'
                    )
                )
                ->get(
                    [
                    'productos.clave_ct',
                    'productos.nombre',
                    'productos.descripcion_corta',
                    'categorias.nombre as categoria',
                    'subcategorias.nombre as subcategoria',
                    'productos.precio_unitario',
                    'productos.enlace',
                    'productos.imagen',
                    'productos.existencias',
                    'productos.precio_unitario',
                    'productos.existencias',
                    'margenes_por_producto.margen_utilidad as margen'
                ]
            );
            //dd(count($data['productos']));
        return view('wp.productosXalapa', compact('data'));
    }
}