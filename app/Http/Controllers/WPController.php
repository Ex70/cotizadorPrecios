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
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 15)
                ->where('existencias.existencias', '>', 0)
                // ->where('productos.clave_ct', '=', [])
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
                    'productos.precio_unitario',
                    'existencias.almacen_id as almacen',
                    'existencias.existencias as existencias',
                    'margenes_por_producto.margen_utilidad as margen',
                    'productos.created_at'
                ]
            );
        $data['met'] = 1;
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Xalapa - (".$fechaR.")";
        return view('wp.productosXalapa', compact('data'));
    }

    public function wptodos(){
        set_time_limit(0);
        $data['productos'] = 
        Producto::Join('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
        // ->Join('propductos','productos.clave_ct', '=', 'existencias.clave_ct')
        // Join('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->whereNotIn('productos.clave_ct',  Producto::join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                    ->where('productos.estatus', 'Activo')
                    ->where('existencias.almacen_id', '=', 15)
                    ->where('existencias.existencias', '>', 0)
                    ->get(
                        'productos.clave_ct'
                    )
                )
                // ->where('productos.estatus', 'Activo')
                // ->where('productos.existencias', '>', 0)
                // ->whereMonth('productos.created_at', '>=', '03')
                // ->whereYear('productos.created_at', '=', '2023')
                //->whereIn('productos.clave_ct', [])
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
                    'marcas.nombre as marca',
                    'productos.precio_unitario',
                    'margenes_por_producto.margen_utilidad as margen'
                    //'productos.created_at'
                ]
            );
           //dd(count($data['productos']));
        $data['met'] = 2;
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Todos los Almacenes - (".$fechaR.")";
        return view('wp.productosXalapa', compact('data'));
    }

    public function wpxalapa_nuevos_dia(){
        set_time_limit(0);
        $data['productos'] = Producto::Join('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 15)
                ->where('existencias.existencias', '>', 0)
                ->whereDay('productos.created_at','=', date('d'))
                ->whereMonth('productos.created_at', '=', date('m'))
                ->whereYear('productos.created_at', '=', date('Y'))
                // ->where('productos.clave_ct', '=', [])
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
                    'productos.precio_unitario',
                    'existencias.almacen_id as almacen',
                    'existencias.existencias as existencias',
                    'margenes_por_producto.margen_utilidad as margen',
                    'productos.created_at'
                ]
            );
        $data['met'] = 1;
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Xalapa - (".$fechaR.")";
        return view('wp.productosXalapa', compact('data'));
    }
    public function pruebas(){
        set_time_limit(0);
        $data['productos'] = 
        Producto::Join('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
        // ->Join('propductos','productos.clave_ct', '=', 'existencias.clave_ct')
        // Join('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                // ->whereNotIn('productos.clave_ct',  Producto::join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                //     ->where('productos.estatus', 'Activo')
                //     ->where('existencias.almacen_id', '=', 15)
                //     ->where('existencias.existencias', '>', 0)
                //     ->get(
                //         'productos.clave_ct'
                //     )
                // )
                // ->where('productos.estatus', 'Activo')
                // ->where('productos.existencias', '>', 0)
                // ->whereMonth('productos.created_at', '>=', '03')
                // ->whereYear('productos.created_at', '=', '2023')
                //->whereIn('productos.clave_ct', [])
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
                    'marcas.nombre as marca',
                    'productos.precio_unitario'
                    // 'margenes_por_producto.margen_utilidad as margen'
                    //'productos.created_at'
                ]
            );
           //dd(count($data['productos']));
        $data['met'] = 2;
        return view('wp.productosXalapa', compact('data'));
    }

    public function fichas(){
        set_time_limit(0);
        $productos=Producto::join('cateegorias',)
        ->where('existencias','>',0)
            ->where('estatus','Activo')
            ->get();
        dd(sizeof($productos));
        $remove = array(" ","  ","   ","    ", "(", ")", "$", "*", "/",",","IVA","Incluido");
        $client = new Client();
        for($i=0;$i<sizeof($productos);$i++){
            $sku = $productos[$i]->sku;
            $clave_ct = $productos[$i]->clave_ct;
            if($sku==""){
                $sku="NOEXISTE";
            }
            // $website = $client->request('GET', 'https://www.zegucom.com.mx/?cons='.$sku.'&mod=search&reg=1');
            $website = $client->request('GET', 'https://www.zegucom.com.mx/productos/search?search='.$sku.'');
            $result = $website->filter('.search-price-now > .search-price-now-value ');
            // $result = $website->filter('.price-text > .result-price-search');
            // $precios[$i] = $result->count() ? str_replace($remove, "", $website->filter('.price-text > .result-price-search')->first()->text()) : $precios[$i] = 0;
            $precios[$i] = $result->count() ? str_replace($remove, "", $website->filter('.search-price-now > .search-price-now-value ')->first()->text()) : $precios[$i] = 0;
            $productoZegucom = Zegucom::updateOrCreate(
                ['sku'=>$sku, 'clave_ct'=>$clave_ct],
                ['precio_unitario'=>$precios[$i]]
            );
        }
        // dd($precios);
        return $precios;
        // $data['precios'] = $precios;
        // dd($data['precios']);
        // $data['categoria'] = $request->get('filtro1');
        // $data['subcategoria'] = $request->get('filtro2');
        // $data['productos'] = DB::table('productos')->select('id','descripcion')->where('estatus','Activo')->where('categoria',$test)->where('subcategoria',$test2)->orderBy('id', 'ASC')->get()->toArray();
        // $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        // $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        // return view('filtrosmipc',compact('data'));
    }
}