<?php

namespace App\Http\Controllers;

use App\Models\Margenes;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


class ProductosXalapaController extends Controller{
    public function cartaXalapa(){
        $data = Producto::Join('existencias', 'productos.clave_ct', '=', 'existencias.clave_ct') 
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('subcategorias', 'productos.subcategoria_id', '=', 'subcategorias.id')
            ->join('promociones', 'productos.clave_ct', '=', 'promociones.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->where('existencias.almacen_id', '=', 15)
            ->where('existencias.almacen_id', '=', 34)
            ->where('existencias.existencias', '>', 0)
            ->orderBy('existencias.existencias', 'desc')
            ->get([
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'productos.nombre',
                'productos.sku',
                'productos.clave_ct',
                'productos.precio_unitario',
                'existencias.existencias as existencias',
                'promociones.descuento',
                'productos.enlace'
            ])
            ->toArray();
        $data = $this->paginate($data, 20);
        $data->withPath('/margenes/mayor');

        //dd($data);

            
        //DB::select("SELECT productos.clave_ct, productos.nombre, categorias.nombre AS categoria, subcategorias.nombre AS subcategoria, marcas.nombre AS marca, productos.enlace, productos.imagen, productos.existencias, margenes.margen_utilidad AS margen FROM productos INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategorias.id INNER JOIN marcas ON productos.marca_id = marcas.id INNER JOIN margenes ON (productos.categoria_id = margenes.categoria_id AND productos.subcategoria_id = margenes.subcategoria_id AND productos.marca_id = margenes.marca_id) WHERE productos.estatus = 'Activo' AND margenes.margen_utilidad > 0.1 AND productos.existencias > 0 LIMIT 0,20;"); 
        return view('cartas.cartas', compact('data'));
        }

    public function paginate($items, $perPage = 20, $page = null){
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage ;
        $itemstoshow = array_slice($items , $offset , $perPage);
        return new LengthAwarePaginator($itemstoshow ,$total ,$perPage);
    }
}

