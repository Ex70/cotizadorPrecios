<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Woocommerce;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WPController extends Controller
{
    public function wpxalapa(){
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
        // $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->leftJoin('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->leftJoin('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->leftJoin('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->leftjoin('promociones', 'promociones.clave_ct', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 50)
                ->where('existencias.existencias', '>', 0)
                ->whereIn('productos.clave_ct',['ACCNCB860'])
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
                    'promociones.fecha_inicio as inicio',
                    'promociones.fecha_fin as fin',
                    'promociones.descuento as descuento'
                ]
            );
        $data['met'] = 1;
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Xalapa - (".$fechaR.")";
        return view('wp.productosXalapa', compact('data'));
    }

    public function wptodos(){
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
        // $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->leftJoin('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->leftJoin('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->leftJoin('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->leftjoin('promociones', 'promociones.clave_ct', 'productos.clave_ct')
                ->whereNotIn('productos.clave_ct',  Producto::join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                    ->where('productos.estatus', 'Activo')
                    ->where('existencias.almacen_id', '=', 50)
                    ->where('existencias.existencias', '>', 0)
                    ->get(
                        'productos.clave_ct'
                    )
                )
                // ->where('productos.estatus', 'Activo')
                // ->where('productos.existencias', '>', 0)
                // ->whereMonth('productos.created_at', '>=', '03')
                // ->whereYear('productos.created_at', '=', '2023')
                ->whereIn('productos.clave_ct', ['ACCNCB860'])
                ->groupBy('clave_ct')
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
                    'margenes_por_producto.margen_utilidad as margen',
                    'promociones.fecha_inicio as inicio',
                    'promociones.fecha_fin as fin',
                    'promociones.descuento as descuento'
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
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::Join('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct', 'right') 
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
        // $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->join('promociones', 'promociones.clave_ct', 'productos.clave_ct')
                // ->whereNotIn('productos.clave_ct',  Producto::join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                //     ->where('productos.estatus', 'Activo')
                //     ->where('existencias.almacen_id', '=', 15)
                //     ->where('existencias.existencias', '>', 0)
                //     ->get(
                //         'productos.clave_ct'
                //     )
                // )
                ->where('productos.estatus', 'Activo')
                // ->where('productos.existencias', '>', 0)
                ->whereDate('promociones.fecha_fin', '>=', $fecha)
                // ->whereYear('productos.created_at', '=', '2023')
                // ->where('productos.clave_ct', '=', 'SOFEST2040')
                //->whereIn('productos.clave_ct', ['SERDEL2730', 'SERDEL2140', 'SERDEL2800', 'SERDEL2400', 'SERDEL2340', 'SERDEL2720', 'SERDEL2700', 'SERDEL2710', 'SERDEL2930', 'SERDEL2440'])
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
                    // 'margenes_por_producto.margen_utilidad as margen',
                    'promociones.fecha_inicio as inicio',
                    'promociones.fecha_fin as fin',
                    'promociones.descuento as descuento'
                ]
                    );
                    //->toArray();
           //dd(count($data['productos']));
           //dd($data['productos'][0]['inicio']);
        // $data['productos'][0]['dia_inicio'] = Carbon::createFromFormat('Y-m-d', $data['productos'][0]['inicio'])
        // ->format('d/m/Y');

        // dd($data['productos'][0]['dia_inicio']);
        $data['met'] = 1;
        return view('wp.productosXalapa', compact('data'));
    }


    public function fichas(){
        set_time_limit(0);
        $data['productos']=Producto::join('categorias','productos.categoria_id','=','categorias.id')
            ->join('subcategorias','productos.subcategoria_id','=','subcategorias.id')
            ->join('marcas','productos.marca_id','=','marcas.id')
            ->where('productos.estatus','Activo')
            ->where('productos.existencias','>',0)
            ->get([
                'woocommerce.idWP',
                'productos.clave_ct',
                'productos.precio_unitario',
                'margenes_por_producto.margen_utilidad',
                'promociones.descuento',
                'promociones.fecha_inicio',
                'promociones.fecha_fin'
            ]
        );
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


    public function individual(Request $request){
        $clave = $request->clavect;
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
        // $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->leftJoin('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->leftJoin('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->leftJoin('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                // ->where('existencias.almacen_id', '=', 50)
                ->where('existencias.existencias', '>', 0)
                ->where('productos.clave_ct', '=', $clave)
                // ->groupBy('clave_ct')
                // ->take(1)
                // ->orderBy('existencias.almacen_id', 'desc')
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
                    'promociones.fecha_inicio as inicio',
                    'promociones.fecha_fin as fin',
                    'promociones.descuento as descuento',
                ]
            );
            if ($request->has('clavect')) {
            }
        $data['met'] = 1;
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Producto ".$clave." - (".$fechaR.")";
        return view('wp.producto_individual', compact('data'));
    }

    public function wp_imagenes(){
        set_time_limit(0);
        $data['productos'] = Producto::where('productos.estatus', 'Activo')
                ->where('productos.existencias', '>', 0)
                ->whereIn('productos.clave_ct',['DDUSAN430'])
                //->where('productos.clave_ct', '=', '')
                // ->groupBy('clave_ct')
                // ->take(1)
                // ->orderBy('existencias.almacen_id', 'desc')
                ->get(['productos.clave_ct',]
            );
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Enlaces de Imagenes - (".$fechaR.")";
        // dd($data['productos'][0]['clave_ct']);
        $aux = '//';
        // for ($i = 0; $i < sizeof($data['productos']); $i++) {
            // return Storage::download('http:'.$aux.'ctonline.mx/img/productos/'.$data['productos'][$i]['clave_ct'].'.jpg', $data['productos'][$i]['clave_ct'] );
            // return Storage::download('https://ctonline.mx/img/productos/MONBNQ1240.jpg', $data['productos'][$i]['clave_ct']);
        // }
        return view('wp.wp_imagenes', compact('data'));

    }

    public function wp_promociones_faltantes(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['xalapa'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct') 
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
        // $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->join('promociones', 'promociones.clave_ct', 'productos.clave_ct')
                ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 50)
                ->where('productos.existencias', '>', 0)
                ->whereNotIn('productos.clave_ct', Woocommerce::get('woocommerce.clave_ct'))
                ->groupBy('clave_ct')
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
                    'promociones.fecha_inicio as inicio',
                    'promociones.fecha_fin as fin',
                    'promociones.descuento as descuento',
                ]
                    );
        $data['met'] = 1;

        $data['resto'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct') 
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
        // $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->join('promociones', 'promociones.clave_ct', 'productos.clave_ct')
                ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 53)
                ->whereNotIn('productos.clave_ct',  Producto::join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                    ->where('productos.estatus', 'Activo')
                    ->where('existencias.almacen_id', '=', 50)
                    ->where('existencias.existencias', '>', 0)
                    ->get(
                        'productos.clave_ct'
                    )
                )
                ->where('productos.existencias', '>', 0)
                ->whereNotIn('productos.clave_ct', Woocommerce::get('woocommerce.clave_ct'))
                ->groupBy('clave_ct')
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
                    'promociones.fecha_inicio as inicio',
                    'promociones.fecha_fin as fin',
                    'promociones.descuento as descuento',
                ]
                    );
        return view('wp.productosXalapa', compact('data'));


        }
}