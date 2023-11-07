<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Woocommerce;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Marca;
use Maatwebsite\Excel\Concerns\ToArray;

class WPController extends Controller
{
    public function wpxalapa(){
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
        // $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->leftjoin('promociones', 'promociones.clave_ct', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 50)
                // ->whereIn('existencias.almacen_id', [50, 53])
                // ->whereIN('productos.categoria_id', [634, 633, 635, 636, 873, 9] )
                // ->where('existencias.existencias', '>', 0)
                ->whereIn('productos.clave_ct',['CAMNNN020', 'KITCNN070', 'ACCEVL140'])
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
                    'existencias.almacen_id as almacen',
                    'existencias.existencias as existencias',
                    // 'productos.existencias as existencias',
                    'margenes_por_producto.margen_utilidad as margen',
                    'promociones.fecha_inicio as inicio',
                    'promociones.fecha_fin as fin',
                    'promociones.descuento as descuento'
                ]
            );
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Xalapa - (".$fechaR.")";
        return view('wp.wp_productos', compact('data'));
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
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 53)
                ->where('existencias.existencias', '>', 0)
                // ->where('productos.estatus', 'Activo')
                // ->where('productos.existencias', '>', 0)
                // ->whereMonth('productos.created_at', '>=', '03')
                // ->whereYear('productos.created_at', '=', '2023')
                // ->whereIn('productos.clave_ct', [])
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
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Todos los Almacenes - (".$fechaR.")";
        return view('wp.productosXalapa', compact('data'));
    }

    public function wp_nuevos_mes(){
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50])
            ->where('existencias.existencias', '>', 0)
            ->whereMonth('productos.created_at', date('m'))
            ->whereYear('productos.created_at', date('Y'))
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                'existencias.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Xalapa - (".$fechaR.")";
        // return view('wp.productosXalapa', compact('data'));
        return view('wp.wp_productos', compact('data'));
    }

    public function wp_nuevos_dia(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50])
            ->where('existencias.existencias', '>', 0)
            ->whereDay('productos.created_at', date('d'))
            ->whereMonth('productos.created_at', date('m'))
            ->whereYear('productos.created_at', date('Y'))
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                'existencias.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Xalapa - (".$fechaR.")";
        // return view('wp.productosXalapa', compact('data'));
        return view('wp.wp_productos', compact('data'));
    }
    public function pruebas(){
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [15])
            ->where('existencias.existencias', '>', 0)
            ->whereIn('productos.clave_ct', Woocommerce::get('woocommerce.clave_ct'))
            // ->where('productos.existencias', '>', 0)
            // ->where('productos.clave_ct', '=', $clave)
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                'existencias.existencias as existencias',
                // 'productos.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        // dd($data['productos']);
            // dd($data['productos']);

        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Producto Xalapa - (".$fechaR.")";
        return view('wp.wp_productos', compact('data'));
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
        // $clave = 'ACCACO050';
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50,53])
            ->where('existencias.existencias', '>', 0)
            // ->where('productos.existencias', '>', 0)
            ->where('productos.clave_ct', '=', $clave)
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                // 'existencias.existencias as existencias',
                'productos.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        // dd($data['productos']);
            // dd($data['productos']);
            if ($request->has('clavect')) {
            }
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Producto ".$clave." - (".$fechaR.")";
        return view('wp.producto_individual', compact('data'));
    }

    public function wp_imagenes(){
        set_time_limit(0);
        // $data['productos'] = Producto::join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
        $data['productos'] = Producto::where('productos.estatus', 'Activo')
                // ->where('productos.estatus', 'Activo')
                ->whereIn('productos.clave_ct',['PANHSE1410','PANSMG4350'])
                // ->whereIN('productos.categoria_id', [616, 781])
                // ->where('productos.marca_id', '=', 5)
                // ->take(1)
                // ->orderBy('existencias.almacen_id', 'desc')
                // ->where('productos.estatus', 'Activo')
                // ->whereIn('productos.subcategoria_id', [978, 672, 839, 977, 1106, 1108, 1109, 1110])
                //->where('productos.subcategoria_id', '=', 978)
                // ->where('existencias.almacen_id', '=', 50)
                // ->where('existencias.existencias', '>', 3)
                ->get(['productos.clave_ct']
            );
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Enlaces de Imagenes - (".$fechaR.")";
        return view('wp.wp_imagenes', compact('data'));

    }

    public function wp_promociones_faltantes(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50])
            ->where('existencias.existencias', '>', 0)
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereNotIn('productos.clave_ct', Woocommerce::get('woocommerce.clave_ct'))
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                // 'existencias.existencias as existencias',
                'productos.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        $data['titulo'] = "EHS - WP - Promociones Faltantes - (".$fecha.")";
        return view('wp.wp_productos', compact('data'));
        }

    public function wp_inventario(){
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            // ->leftjoin('promociones', 'promociones.clave_ct', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            ->where('existencias.almacen_id', '=' , 50)
            ->get([
                'productos.clave_ct',
                'existencias.existencias',
                'productos.estatus',
                'productos.clave_ct',
                'productos.precio_unitario',
                'margenes_por_producto.margen_utilidad as margen',
                // 'promociones.fecha_inicio as inicio',
                // 'promociones.fecha_fin as fin',
                // 'promociones.descuento as descuento'
                ]);
        $data['titulo'] = "EHS - WP - Inventario - (".$fechaR.")";        
        return view('wp.wp_inventario', compact('data'));
    }

    public function wp_tipos(){
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        $data['xalapa'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.estatus', '=', 'Activo')
            ->get([
                'woocommerce.clave_ct',
                ]);
        $data['resto'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', '=', 'Activo')
            ->whereNotIn('woocommerce.clave_ct',  Woocommerce::join('existencias', 'existencias.clave_ct', '=', 'woocommerce.clave_ct')
                ->where('existencias.almacen_id', '=', 50)
                ->get([
                    'woocommerce.clave_ct'
                ])
                )
            ->groupBy('productos.clave_ct')
            ->get([
                'woocommerce.clave_ct',
                'existencias.almacen_id as almacen'
            ]);
        // dd($data['resto']);
        $data['titulo'] = "EHS - WP - Tipos - (".$fechaR.")";        
        return view('wp.wp_tipos', compact('data'));
    }

    public function wp_landing_(){
        set_time_limit(0);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['xalapa'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.estatus', '=', 'Activo')
            ->get([
                'woocommerce.idWP'
                ]);
        // dd($data['resto']);
        $data['titulo'] = "EHS - WP - Tipos - (".$fechaR.")";        
        return view('wp.wp_tipos', compact('data'));
    }


    public function wp_promociones_vigentes(){
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        // dd('Bien');
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            ->whereIn('existencias.almacen_id', [50])
            ->where('promociones.fecha_fin', '>=', $fechaR)
            ->where('productos.estatus', '=', 'Activo')
            ->where('existencias.existencias', '>', 0)
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                'existencias.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento'
                ]);
        // dd('Bien');
        $data['titulo'] = "EHS - WP - Promociones - (".$fechaR.")";        
        return view('wp.wp_promociones', compact('data'));
    }

    public function wp_bloque_promociones(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto1 = 'Bloques de Promociones';
        Storage::put('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            // ->whereIN('productos.subcategoria_id', [978, 1008])
            // ->where('existencias.almacen_id', '=', 50)
            ->get([
                'woocommerce.idWP',
                'productos.clave_ct',
                'productos.nombre',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'woocommerce.fecha_inicio as inicio',
                'woocommerce.fecha_fin as fin'
                ]);
        // dd(sizeof($data['productos']));
        // for ($i = 0; $i < sizeof($data['productos']); $i++) {
            // dd($data['productos'][$i]);
            // dd($data['productos'][$i]['idWP']);
        //     if(($i+1) != sizeof($data['productos'])){
        //         $texto2 = $data['productos'][$i]['idWP']. ', ';
        //         Storage::append("bloques_promociones.txt", $texto2, NULL);
        //     }else{
        //         $texto2 = $data['productos'][$i]['idWP'];
        //         Storage::append("bloques_promociones.txt", $texto2, NULL);
        //     }
        // }
        // $texto3 = '"][/vc_column][/vc_row]';
        // Storage::append("bloques_promociones.txt", $texto3, NULL);
        $this->wp_bloque_audifonos();
        $this->wp_bloque_almacenamiento();
        $this->wp_bloque_bocinas();
        $this->wp_bloque_diademas();
        $this->wp_bloque_gabinetes();
        $this->wp_bloque_impresion();
        $this->wp_bloque_kit();
        $this->wp_bloque_laptop();
        $this->wp_bloque_ram();
        $this->wp_bloque_monitores();
        $this->wp_bloque_mouse();
        $this->wp_bloque_silla();
        $this->wp_bloque_smartwatch();
        $this->wp_bloque_tabletas();
        $this->wp_bloque_telacdos();
        $this->wp_bloque_videovigilancia();
        $data['titulo'] = "EHS - WP - Bloque Promociones - (".$fecha.")";        
        return view('wp.wp_bloque_promocion', compact('data'));
    }

    public function wp_bloque_audifonos(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de audifonos';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [978, 1008])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_bocinas(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de bocinas';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [41, 1107])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_diademas(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de diademas';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [839, 977])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_gabinetes(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de gabinetes';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [832])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_impresion(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de impresion';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [792, 793, 131, 121, 53, 165])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_kit(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de kit';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [517, 833])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_laptop(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de laptop';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [585, 789])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_ram(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de ram';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [101])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_monitores(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de monitores';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [107, 324, 790, 985, 1103])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_mouse(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de mouse';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [110, 834])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_silla(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de silla';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [901])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_smartwatch(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de smartwatch';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [976])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_tabletas(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de tabletas';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [591])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_telacdos(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de telacdos';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [157, 837])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

public function wp_bloque_videovigilancia(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de videovigilancia';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.categoria_id', [632])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

    public function wp_bloque_almacenamiento(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de Almacenamiento';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [100, 606, 608, 669, 64, 605, 870])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }

    public function wp_promocion_dia(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::rightJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50])
            ->where('existencias.existencias', '>', 0)
            // ->whereDay('promociones.updated_at', date('d'))
            ->whereMonth('promociones.updated_at', date('m'))
            ->whereYear('promociones.updated_at', date('Y'))
            ->whereIn('productos.clave_ct', Woocommerce::get('woocommerce.clave_ct'))
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                'existencias.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
            // dd($data['productos']);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Promociones - (".$fechaR.")";
        // return view('wp.productosXalapa', compact('data'));
        return view('wp.wp_promociones', compact('data'));
    }

    public function wp_filtros()
    {
        $data['categorias'] = Categoria::distinct('nombre')->where('nombre','not like',"% - 2%")->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['productos'] = '';
        return view('wp.wp_filtros_productos', compact('data'));
    }

    public function wp_productos_filtros(Request $request){
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $test3 = $request->get('filtro3');
        // dd($test2);
        // $test = 10;
        // $test2 = 27;
        // $test3 = 98;
        if ($test3 == 'z') {
            $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50])
            ->where('existencias.existencias', '>', 0)
            ->whereNotIn('productos.clave_ct', Woocommerce::get('woocommerce.clave_ct'))
            ->where('productos.categoria_id', '=', $test)
            ->where('productos.subcategoria_id', '=', $test2)
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                'existencias.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        } else {
            $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50])
            ->where('existencias.existencias', '>', 0)
            ->whereNotIn('productos.clave_ct', Woocommerce::get('woocommerce.clave_ct'))
            ->where('productos.categoria_id', '=', $test)
            ->where('productos.subcategoria_id', '=', $test2)
            ->where('productos.marca_id', '=', $test3)
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                'existencias.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        }
            // dd($data['productos']);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos por Categoria - (".$fechaR.")";
        $data['categorias'] = Categoria::distinct('nombre')->where('nombre','not like',"% - 2%")->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        // return view('wp.productosXalapa', compact('data'));
        return view('wp.wp_filtros_productos', compact('data'));
    }

    public function wp_precios(){
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        // dd('Bien');
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            ->whereIn('existencias.almacen_id', [50])
            ->where('productos.existencias', '>=', 0)
            // ->where('productos.estatus', 'Descontinuado')
            // ->where('promociones.fecha_fin', '>=', $fechaR)
            ->get([
                'productos.clave_ct',
                'productos.precio_unitario',
                // 'existencias.almacen_id as almacen',
                'productos.estatus',
                'productos.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                ]);
        // dd('Bien');
        $data['titulo'] = "EHS - WP - Precios - (".$fechaR.")";        
        return view('wp.wp_precios', compact('data'));
    }



    public function wp_bloque_nuevos(){
        $mes = date('m');
        $año = date('Y');
        $texto1 = 'PRODUCTOS NUEVOS';
        Storage::put('bloques_Productos Nuevos.txt', $texto1);
        set_time_limit(0);
        $texto_inicial = 'Bloque de productos nuevos';
        Storage::append('bloques_Productos Nuevos.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_Productos Nuevos.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            ->where('productos.estatus', '=' , 'Activo')
            ->where('productos.existencias', '>' , 0)
            ->whereMonth('productos.created_at', '>=', 06)
            ->whereYear('productos.created_at', '=', 2023)
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_Productos Nuevos.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_Productos Nuevos.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_Productos Nuevos.txt", $texto3, NULL);
        $texto4 = 'Total de productos Nuevos: ' .$i;
        Storage::append("bloques_Productos Nuevos.txt", $texto4);
        dd('Bloque Listo');
    }

    public function wp_actualizar_precio_individual_1(Request $request){
        $clave = $request->clavect;
        // $clave = 'MONDLL870';
        $data['productos'] = Producto::join('woocommerce', 'productos.clave_ct', '=', 'woocommerce.clave_ct')
        ->leftJoin('promociones', 'productos.clave_ct', '=', 'promociones.clave_ct')
        ->leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
        ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->where('existencias.existencias', '>', 0)
                ->where('productos.clave_ct', '=', $clave)
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 50)
                ->get(
                    [
                    'woocommerce.idWP',
                    'productos.clave_ct',
                    'productos.nombre',
                    'productos.precio_unitario',
                    'margenes_por_producto.margen_utilidad as margen',
                    'promociones.descuento',
                    'promociones.fecha_fin',
                    'woocommerce.precio_venta',
                    'woocommerce.precio_venta_rebajado',
                    'existencias.existencias as existencia'
                ]
                    );
            // ->toArray();

            
        // dd($data['productos']);
        // dd(($data['productos'][0]['precio_unitario']));
        if ($request->has('clavect')) {
            if ($data['productos']== null) {
                $texto1 = 'Producto no encontrado en almacen 50';
                dd($texto1);
            }else{
                if(isset($data['productos'][0]['margen'])){
                    if(isset($data['productos'][0]['descuento'])){
                        $data['productos'][0]['precio_rebajado'] = round(((($data['productos'][0]['precio_unitario'])*(($data['productos'][0]['margen'])+1))*((100-($data['productos'][0]['descuento']))/100)),2);
                        $data['productos'][0]['precio_normal']= round((($data['productos'][0]['precio_unitario'])*(($data['productos'][0]['margen'])+1)),2);
                    }else{
                        $data['productos'][0]['precio_rebajado'] = '';
                        $data['productos'][0]['precio_normal']= round((($data['productos'][0]['precio_unitario'])*(($data['productos'][0]['margen'])+1)),2);
                    }
                }else{
                    if(isset($data['productos'][0]['descuento'])){
                        $data['productos'][0]['precio_rebajado'] = round((($data['productos'][0]['precio_unitario'])*(1.1111)*((100-($data['productos'][0]['descuento']))/100)),2);
                        $data['productos'][0]['precio_normal']= round((($data['productos'][0]['precio_unitario'])*(1.1111)),2);
                    }else{
                        $data['productos'][0]['precio_rebajado'] = '';
                        $data['productos'][0]['precio_normal']= round((($data['productos'][0]['precio_unitario'])*(1.1111)),2);
                    }
                }
                // dd($data['productos'][0]['precio_normal']);
            }
            
        }
        return view('wp.wp_carta_act_precios', compact('data'));
    }

    public function wp_productos_faltantes_50(){
        // $clave = 'ACCACO050';
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.almacen_id', '=', 50)
            ->where('existencias.existencias', '>', 0)
            ->where('productos.estatus', '=', 'Activo')
            ->whereIN('productos.categoria_id', [830, 781] )
            ->whereNotIn('productos.clave_ct', Woocommerce::get('woocommerce.clave_ct'))
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'productos.descripcion_corta',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.precio_unitario',
                'productos.enlace',
                'productos.imagen',
                'existencias.almacen_id as almacen',
                // 'existencias.existencias as existencias',
                'productos.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        // dd($data['productos']);
            // dd($data['productos']);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Faltantes en WP - (".$fechaR.")";
        return view('wp.wp_productos', compact('data'));
    }



    public function wp_actualizar_promociones(){
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
                ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->leftjoin('promociones', 'promociones.clave_ct', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 50)
                ->where('existencias.existencias', '>', 0)
                ->get(
                    [
                    'productos.clave_ct',
                    'productos.precio_unitario',
                    'existencias.almacen_id as almacen',
                    'existencias.existencias as existencias',
                    // 'productos.existencias as existencias',
                    'margenes_por_producto.margen_utilidad as margen',
                    'promociones.fecha_inicio as inicio',
                    'promociones.fecha_fin as fin',
                    'promociones.descuento as descuento'
                ]
            );
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Xalapa - (".$fechaR.")";
        return view('wp.wp_productos', compact('data'));
    }

    public function actualizarPromociones(Request $request){
        set_time_limit(0);
        $clave = $request->clavect;
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::join('woocommerce', 'productos.clave_ct', '=', 'woocommerce.clave_ct')
        ->leftJoin('promociones', 'productos.clave_ct', '=', 'promociones.clave_ct')
        ->leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
        ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
        ->where('productos.clave_ct', '=', $clave)
        ->where('existencias.existencias', '>', 0)
        ->where('productos.estatus', 'Activo')
        ->where('existencias.almacen_id', '=', 50)
        ->whereDate('promociones.fecha_fin','>=',$fecha)
        ->get([
            'woocommerce.idWP',
            'productos.clave_ct',
            'productos.nombre',
            'productos.precio_unitario',
            'margenes_por_producto.margen_utilidad as margen',
            'promociones.descuento',
            'promociones.fecha_fin as fecha_fin',
            'promociones.fecha_inicio as fecha_inicio',
            'woocommerce.fecha_inicio as fecha_iwp',
            'woocommerce.fecha_fin as fecha_fwp',
            'woocommerce.precio_venta_rebajado as precio_desc_wp',
            'woocommerce.precio_venta as precio_wp'
        ])
        ->ToArray();

        if ($request->has('clavect')) {
            if ($data['productos']== null) {
                $texto1 = 'Producto no encontrado en almacen 50';
                dd($texto1);
            }else{
                if(isset($data['productos'][0]['margen'])){
                    if(isset($data['productos'][0]['descuento'])){
                        $data['productos'][0]['precio_rebajado'] = round(((($data['productos'][0]['precio_unitario'])*(($data['productos'][0]['margen'])+1))*((100-($data['productos'][0]['descuento']))/100)),2);
                        $data['productos'][0]['precio_normal']= round((($data['productos'][0]['precio_unitario'])*(($data['productos'][0]['margen'])+1)),2);
                    }else{
                        $data['productos'][0]['precio_rebajado'] = '';
                        $data['productos'][0]['precio_normal']= round((($data['productos'][0]['precio_unitario'])*(($data['productos'][0]['margen'])+1)),2);
                    }
                }else{
                    if(isset($data['productos'][0]['descuento'])){
                        $data['productos'][0]['precio_rebajado'] = round((($data['productos'][0]['precio_unitario'])*(1.1111)*((100-($data['productos'][0]['descuento']))/100)),2);
                        $data['productos'][0]['precio_normal']= round((($data['productos'][0]['precio_unitario'])*(1.1111)),2);
                    }else{
                        $data['productos'][0]['precio_rebajado'] = '';
                        $data['productos'][0]['precio_normal']= round((($data['productos'][0]['precio_unitario'])*(1.1111)),2);
                    }
                }
                // dd($data['productos'][0]['precio_normal']);
            }
            $data['productos'][0]['precio_venta'] = round((($data['productos'][0]['precio_unitario'])*((100-($data['productos'][0]['descuento']))/100)),2);
        }
        return view('wp.wp_carta_act_promo', compact('data'));
    }

    public function wp_bloque_computadoras(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de Computadoras';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [585, 589, 28, 589])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }
    public function wp_bloque_componentes(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de Componentes';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [611, 613, 64, 605, 606, 917, 189, 79, 517, 101, 913, 107, 324, 1103, 109, 110, 104, 916, 156, 157, 1027])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }
    public function wp_bloque_gaming(){
        set_time_limit(0);
        $fecha = date('Y')."-".date('m')."-".date('d');
        $texto_inicial = 'Bloque de Gaming';
        Storage::append('bloques_promociones.txt', $texto_inicial);
        $texto1 = '[products columns="4" orderby="menu_order" order="ASC" ids="';
        Storage::append('bloques_promociones.txt', $texto1);
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->join('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('existencias.existencias', '>', 3)
            ->where('existencias.almacen_id', '=', 50)
            ->where('productos.clave_ct', '!=' , '')
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->whereIN('productos.subcategoria_id', [287, 288, 289, 291, 292, 295, 296, 448, 450, 283, 285, 449, 293, 284, 286, 290, 294])
            ->get([
                'woocommerce.idWP',
                ]);
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            if(($i+1) != sizeof($data['productos'])){
                $texto2 = $data['productos'][$i]['idWP']. ', ';
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }else{
                $texto2 = $data['productos'][$i]['idWP'];
                Storage::append("bloques_promociones.txt", $texto2, NULL);
            }
        }
        $texto3 = '"][/vc_column][/vc_row]';
        Storage::append("bloques_promociones.txt", $texto3, NULL);
        $texto4 = 'Total de productos en landing: ' .$i;
        Storage::append("bloques_promociones.txt", $texto4);
    }


}