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
                ->where('existencias.existencias', '>', 0)
                // ->whereIn('productos.clave_ct',[])
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
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50])
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
                'promociones.descuento as descuento',
                ]);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - WP - Productos Xalapa - (".$fechaR.")";
        // return view('wp.productosXalapa', compact('data'));
        return view('wp.producto_individual', compact('data'));
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
            ->whereIN('existencias.almacen_id', [50])
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
        $data['productos'] = Producto::where('productos.estatus', 'Activo')
                ->where('productos.existencias', '>', 0)
                ->whereIn('productos.clave_ct',['IMPXRX2270', 'IMPXRX2290', 'NBKAPC1620', 'NBKVIC720', 'NBKAPC2010', 'ACCPOL630', 'NBKAPC2020', 'IMPXRX2490', 'NBKAPC2030', 'NBKAPC2480', 'NBKAPC1940', 'MALSTY020', 'ACCPOL240', 'MALSTY010', 'IMPXRX2520', 'BATAPC110', 'NBKAPC2430', 'IMPXRX2570', 'IMPXRX2660', 'MALSTY040', 'IMPXRX2230', 'IMPXRX2560', 'ACCMST4240', 'NBKAPC1950', 'MALGEN3670', 'NBKAPC2530', 'IMPXRX2550', 'MOUMST1660', 'NBKAPC1960', 'NBKDTS500', 'BOCMST3130', 'IMPXRX2580', 'ACCSTY360', 'IMPXRX2590', 'IMPXRX2650', 'BOCMST3480', 'ACCMST4370', 'BOCSTY440', 'BOCSTY430', 'BOCMST3270', 'TARMER030', 'NBKAPC1690', 'TELPAN130', 'ACCSTY330', 'IMPXRX2610', 'SOFEST2070', 'TELPAN050', 'IMPXRX2600', 'TELPAN120', 'SOFEST2080', 'NBKAPC1820', 'SOFNRT1620', 'ACCMST4130', 'TELPAN055', 'NBKAPC420', 'SOFNRT1600', 'SOFNRT1610', 'SOFBIT1360', 'SOFNRT1630', 'ACCHIG080', 'TELPAN105', 'ACCPTS1060', 'BOCMST250', 'BOCHIG140', 'SOFEST2100', 'SOFEST2090', 'ACCMST1700', 'ACCPTS1020', 'TARMER010', 'NBKAPC600', 'NBKAPC1980', 'NBKAPC2270', 'IMPXRX2620', 'NBKAPC1460', 'ACCHPE2730', 'ACCMST4270', 'BATAPC030', 'ACCAPC080', 'NBKCYP750', 'NBKQIA010', 'ACCAPC150', 'REGAPC030', 'NBKCYP960', 'BATAPC620', 'BATAPC240', 'NBKAPC2170', 'BATAPC180', 'IMPEPS3270', 'DDUHPE880', 'SWTARU210', 'SWTARU190', 'STOHPE610', 'ACCARU280', 'NBKAPC1450', 'NBKAPC2260', 'NBKAPC2130', 'SWTARU150', 'BATAPC340', 'NBKAPC2050', 'PANHSE1430', 'PLOEPS240', 'PLOEPS260', 'SCAEPS780'])
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
                'existencias.existencias as existencias',
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
            ->where('productos.clave_ct', '!=' , '')
            ->get([
                'productos.clave_ct',
                'productos.existencias',
                'productos.estatus'
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

    public function wp_inventario_50(){
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            ->where('existencias.almacen_id', '=', 50)
            ->get([
                'productos.clave_ct',
                'productos.existencias',
                'productos.estatus'
                ]);
        $data['titulo'] = "EHS - WP - Inventario - (".$fechaR.")";        
        return view('wp.wp_inventario', compact('data'));
    }

    public function wp_promociones_vigentes(){
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        // dd('Bien');
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            ->whereIn('existencias.almacen_id', [50,53])
            ->where('existencias.existencias', '>', 0)
            ->where('promociones.fecha_fin', '>=', $fechaR)
            ->get([
                'productos.clave_ct',
                'productos.precio_unitario',
                'existencias.almacen_id as almacen',
                'productos.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        // dd('Bien');
        $data['titulo'] = "EHS - WP - Precios - (".$fechaR.")";        
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
            // ->where('productos.estatus', 'Activo')
            // ->where('promociones.fecha_fin', '>=', $fechaR)
            ->get([
                'productos.clave_ct',
                'productos.precio_unitario',
                // 'existencias.almacen_id as almacen',
                'productos.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                ]);
        // dd('Bien');
        $data['titulo'] = "EHS - WP - Precios - (".$fechaR.")";        
        return view('wp.wp_precios', compact('data'));
    }
}