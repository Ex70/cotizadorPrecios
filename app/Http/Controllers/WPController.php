<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Woocommerce;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                ->whereIn('productos.clave_ct',['MONSTY120', 'MONSTY190', 'MONASS1280', 'MONVGO070', 'MONSMG1580', 'MONVGO110', 'MONVGO140', 'MONVGO160', 'CPUYEY090', 'MONRDG010', 'MONYEY140', 'CPUYEY150', 'MONGIG040', 'CPUASS840', 'CPUASS870', 'MONSTY270', 'MONSMG1730', 'MONSMG1740', 'MONASS1480', 'COMASS7850', 'CPUYEY380', 'MONVGO190', 'COMLEV4280', 'MONSMG1810', 'GABBLR150', 'MOUVGO530', 'TECRDG170', 'TECRDG180', 'TECRDG190', 'CONLOG280', 'MOUNCB270', 'MOUNCB360', 'TECVGO120', 'MOUVGO330', 'GABVGO070', 'GABVGO020', 'SILVGO010', 'SILVGO020', 'TECLOG670', 'BOCLOG1370', 'BOCLOG1390', 'KITTMK080', 'MOUNCB370', 'MOUNCB390', 'BOCVGO1070', 'TECVGO150', 'SILVGO090', 'SILVGO080', 'KITVGO110', 'KITVGO140', 'BOCVGO1120', 'GABVGO120', 'TECVGO130', 'SILVGO070', 'GABYEY040', 'TECLOG720', 'MOULOG1910', 'TECGEN970', 'MOUITL780', 'MOULOG2240', 'MOULOG2250', 'MOULOG2280', 'GABASS070', 'MBDASS5090', 'GABNCB070', 'GABNCB080', 'BOCVGO1380', 'BOCVGO1240', 'GABVGO190', 'TECVGO170', 'TECVGO160', 'MOUVGO500', 'MOUVGO510', 'BOCBLR040', 'BOCBLR050', 'BOCBLR100', 'GABBLR180', 'GABBLR190', 'GABBLR230', 'TECBLR110', 'TECBLR120', 'MOUBLR050', 'MOUBLR060', 'MOUBLR070', 'MOUBLR080', 'MOUBLR090', 'SILBLR080', 'SILBLR090', 'KITBLR060', 'BOCMST3210', 'BOCMST3220', 'BOCMST3240', 'BOCMST3300', 'MOUMST1620', 'ACCMST4260', 'KITMST1120', 'BOCMST3250', 'BOCSTY420', 'GABSTY070', 'GABYEY240', 'ACCYEY180', 'SILYEY110', 'SILYEY120', 'SILYEY130', 'SILYEY150', 'SILYEY140', 'SILSTY210', 'SILSTY280', 'KITSTY040', 'SILYEY180', 'MOUMST1670', 'KITASS030', 'KITRDG020', 'MOUMST1680', 'MOURDG030', 'MOURDG040', 'TECRDG030', 'TECRDG070', 'TECRDG080', 'MOUITL820', 'BOCBLR070', 'BOCRDG010', 'BOCRDG020', 'BOCRDG050', 'BOCRDG070', 'BOCRDG080', 'TECCOR360', 'TVIPNY1870', 'GABMSI090', 'MOURDG120', 'MOURDG070', 'MOURDG080', 'MOURDG110', 'TECRDG200', 'TECRDG230', 'TECRDG320', 'BOCRDG130', 'SILNNN040', 'SILBLR160', 'BOCVRT140', 'TECVRT100', 'TECVRT110', 'ACCVRT100', 'SILSTY330', 'SILSTY340', 'ACCASS410', 'BOCASS170', 'BOCASS130', 'ACCTCH9880', 'ACCTCH9950', 'BOCTCH3080', 'BOCTCH3160', 'MOUTCH840', 'MOUTCH850', 'MOUTCH870', 'MOUTCH880', 'MOUTCH910', 'MBDASS5740', 'TVIASS3050', 'BOCLOG2030', 'BOCLOG2020', 'KITMST1320', 'GABCOR1470', 'MOUCOR260', 'ACCCLR230', 'ACCCLR240', 'SILCLR010', 'SILCLR030', 'GABACT190', 'BOCASS180', 'TVIASS3310', 'GABDCO010', 'GABDCO020', 'GABDCO030', 'GABDCO040'])
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
                ->whereIn('productos.clave_ct', ['KITMST1340', 'CPUHPI2060', 'KITMST1300', 'CAMEZV290', 'CAMEZV130', 'CAMEZV140', 'CAMEZV150', 'CAMEZV270', 'CAMEZV350', 'DDUGIG050', 'SOFAPL4600', 'SOFAPL4610', 'ACCHUW420', 'BOCHUW170', 'CAMQIA150', 'TECYEY010', 'TECYEY030', 'TECYEY040', 'BOCYEY040', 'MOUYEY090', 'MOUYEY100', 'TECYEY140', 'MOUYEY110', 'TECYEY160', 'TECYEY170', 'DDUACR150', 'DDUACR090', 'DDUACR100', 'CPUAMD2410', 'CAMDAH2660'])
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

    public function wp_nuevos_mes(){
        set_time_limit(0);
        $mes = date('m');
        $año = date('Y');
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50,53])
            ->where('existencias.existencias', '>', 0)
            ->whereMonth('productos.created_at', '>=', $mes)
            ->whereYear('productos.created_at', '>=', $año)
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
    public function pruebas(){
        set_time_limit(0);
        $data['productos'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->Join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->Join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
            ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->Join('marcas', 'marcas.id', '=', 'productos.marca_id')
            ->leftjoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereIN('existencias.almacen_id', [50,53])
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
            ->whereIN('existencias.almacen_id', [50,53])
            ->where('existencias.existencias', '>', 0)
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
                'existencias.existencias as existencias',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        // dd($data['productos']);
            // dd($data['productos']);
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
                ->whereIn('productos.clave_ct',['MONSTY120', 'MONSTY190', 'MONASS1280', 'MONVGO070', 'MONSMG1580', 'MONVGO110', 'MONVGO140', 'MONVGO160', 'CPUYEY090', 'MONRDG010', 'MONYEY140', 'CPUYEY150', 'MONGIG040', 'CPUASS840', 'CPUASS870', 'MONSTY270', 'MONSMG1730', 'MONSMG1740', 'MONASS1480', 'COMASS7850', 'CPUYEY380', 'MONVGO190', 'COMLEV4280', 'MONSMG1810', 'GABBLR150', 'MOUVGO530', 'TECRDG170', 'TECRDG180', 'TECRDG190', 'CONLOG280', 'MOUNCB270', 'MOUNCB360', 'TECVGO120', 'MOUVGO330', 'GABVGO070', 'GABVGO020', 'SILVGO010', 'SILVGO020', 'TECLOG670', 'BOCLOG1370', 'BOCLOG1390', 'KITTMK080', 'MOUNCB370', 'MOUNCB390', 'BOCVGO1070', 'TECVGO150', 'SILVGO090', 'SILVGO080', 'KITVGO110', 'KITVGO140', 'BOCVGO1120', 'GABVGO120', 'TECVGO130', 'SILVGO070', 'GABYEY040', 'TECLOG720', 'MOULOG1910', 'TECGEN970', 'MOUITL780', 'MOULOG2240', 'MOULOG2250', 'MOULOG2280', 'GABASS070', 'MBDASS5090', 'GABNCB070', 'GABNCB080', 'BOCVGO1380', 'BOCVGO1240', 'GABVGO190', 'TECVGO170', 'TECVGO160', 'MOUVGO500', 'MOUVGO510', 'BOCBLR040', 'BOCBLR050', 'BOCBLR100', 'GABBLR180', 'GABBLR190', 'GABBLR230', 'TECBLR110', 'TECBLR120', 'MOUBLR050', 'MOUBLR060', 'MOUBLR070', 'MOUBLR080', 'MOUBLR090', 'SILBLR080', 'SILBLR090', 'KITBLR060', 'BOCMST3210', 'BOCMST3220', 'BOCMST3240', 'BOCMST3300', 'MOUMST1620', 'ACCMST4260', 'KITMST1120', 'BOCMST3250', 'BOCSTY420', 'GABSTY070', 'GABYEY240', 'ACCYEY180', 'SILYEY110', 'SILYEY120', 'SILYEY130', 'SILYEY150', 'SILYEY140', 'SILSTY210', 'SILSTY280', 'KITSTY040', 'SILYEY180', 'MOUMST1670', 'KITASS030', 'KITRDG020', 'MOUMST1680', 'MOURDG030', 'MOURDG040', 'TECRDG030', 'TECRDG070', 'TECRDG080', 'MOUITL820', 'BOCBLR070', 'BOCRDG010', 'BOCRDG020', 'BOCRDG050', 'BOCRDG070', 'BOCRDG080', 'TECCOR360', 'TVIPNY1870', 'GABMSI090', 'MOURDG120', 'MOURDG070', 'MOURDG080', 'MOURDG110', 'TECRDG200', 'TECRDG230', 'TECRDG320', 'BOCRDG130', 'SILNNN040', 'SILBLR160', 'BOCVRT140', 'TECVRT100', 'TECVRT110', 'ACCVRT100', 'SILSTY330', 'SILSTY340', 'ACCASS410', 'BOCASS170', 'BOCASS130', 'ACCTCH9880', 'ACCTCH9950', 'BOCTCH3080', 'BOCTCH3160', 'MOUTCH840', 'MOUTCH850', 'MOUTCH870', 'MOUTCH880', 'MOUTCH910', 'MBDASS5740', 'TVIASS3050', 'BOCLOG2030', 'BOCLOG2020', 'KITMST1320', 'GABCOR1470', 'MOUCOR260', 'ACCCLR230', 'ACCCLR240', 'SILCLR010', 'SILCLR030', 'GABACT190', 'BOCASS180', 'TVIASS3310', 'GABDCO010', 'GABDCO020', 'GABDCO030', 'GABDCO040'])
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
                ->whereDay('promociones.updated_at', '=', 18)
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

        $data['resto'] = Producto::leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct') 
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
        // $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->join('promociones', 'promociones.clave_ct', 'productos.clave_ct')
                ->Join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                ->where('productos.estatus', 'Activo')
                ->where('existencias.almacen_id', '=', 53)
                ->whereDay('promociones.updated_at', '=', 18)
                ->whereNotIn('productos.clave_ct',  Producto::join('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
                    // ->where('productos.estatus', 'Activo')
                    ->where('existencias.almacen_id', '=', 50)
                    ->where('existencias.existencias', '>', 0)
                    ->get(
                        'productos.clave_ct'
                    )
                )
                // ->where('productos.existencias', '>', 0)
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
        return view('wp.prueba', compact('data'));
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

    public function wp_precios(){
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        // dd('Bien');
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->leftjoin('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            // ->where('existencias.almacen_id', '=', 50)
            ->groupBy('clave_ct')
            ->get([
                'productos.clave_ct',
                'productos.precio_unitario',
                'existencias.almacen_id as almacen',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        // dd('Bien');
        $data['titulo'] = "EHS - WP - Inventario - (".$fechaR.")";        
        return view('wp.wp_precios', compact('data'));
    }

    public function wp_precios_50(){
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::join('woocommerce', 'woocommerce.clave_ct', '=', 'productos.clave_ct')
            ->leftjoin('existencias', 'existencias.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->leftJoin('margenes_por_producto', 'margenes_por_producto.clave_ct', '=', 'productos.clave_ct')
            ->where('productos.clave_ct', '!=' , '')
            ->where('existencias.almacen_id', '=', 50)
            ->groupBy('clave_ct')
            ->get([
                'productos.clave_ct',
                'productos.precio_unitario',
                'existencias.almacen_id as almacen',
                'margenes_por_producto.margen_utilidad as margen',
                'promociones.fecha_inicio as inicio',
                'promociones.fecha_fin as fin',
                'promociones.descuento as descuento',
                ]);
        // dd('Bien');
        $data['titulo'] = "EHS - WP - Inventario - (".$fechaR.")";        
        return view('wp.wp_precios', compact('data'));
    }
}