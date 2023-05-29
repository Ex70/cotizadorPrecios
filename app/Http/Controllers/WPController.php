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
                ->whereIn('productos.clave_ct',[''])
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
                ->whereIn('productos.clave_ct',['ACCACT3140', 'ACCACT4070', 'ACCACT4090', 'ACCACT4110', 'ACCACT4120', 'ACCACT4130', 'ACCACT4140', 'ACCACT4150', 'ACCACT4160', 'ACCACT4170', 'ACCACT4180', 'ACCACT4190', 'ACCACT4200', 'ACCACT4240', 'ACCACT4260', 'ACCACT4270', 'ACCACT4280', 'ACCAZR040', 'ACCAZR220', 'ACCAZR260', 'ACCAZR320', 'ACCBAC130', 'ACCBAC1790', 'ACCBAC2390', 'ACCBAC2410', 'ACCBAC2420', 'ACCBAC3530', 'ACCBAC3550', 'ACCBAC3560', 'ACCBEL160', 'ACCCDM1010', 'ACCCDM1210', 'ACCCDM970', 'ACCDAH1030', 'ACCDAH520', 'ACCDAH560', 'ACCDAH650', 'ACCDAH800', 'ACCDAH820', 'ACCDAT1410', 'ACCDAT1460', 'ACCDAT1480', 'ACCDAT1490', 'ACCDIE010', 'ACCEZV070', 'ACCGEN300', 'ACCGEN320', 'ACCHPI3420', 'ACCHPI880', 'ACCHPI920', 'ACCHPQ050', 'ACCITL045', 'ACCITL1055', 'ACCITL1165', 'ACCITL1430', 'ACCITL1500', 'ACCITL1795', 'ACCITL1990', 'ACCITL200', 'ACCITL2060', 'ACCITL2450', 'ACCITL2570', 'ACCITL2590', 'ACCITL300', 'ACCITL320', 'ACCITL3460', 'ACCITL3840', 'ACCITL3860', 'ACCITL420', 'ACCITL4340', 'ACCITL540', 'ACCITL7010', 'ACCITL810', 'ACCITL920', 'ACCKNS020', 'ACCKNS910', 'ACCKSA010', 'ACCKSA040', 'ACCLCS230', 'ACCMAC2550', 'ACCMAC910', 'ACCMST1060', 'ACCMST1070', 'ACCMST1350', 'ACCMST1680', 'ACCMST2220', 'ACCMST4010', 'ACCNCB010', 'ACCNCB020', 'ACCNCB070', 'ACCNCB1000', 'ACCNCB1100', 'ACCNCB1130', 'ACCNCB1170', 'ACCNCB1210', 'ACCNCB1220', 'ACCNCB160', 'ACCNCB170', 'ACCNCB210', 'ACCNCB220', 'ACCNCB350', 'ACCNCB480', 'ACCNCB780', 'ACCNCB790', 'ACCNCB930', 'ACCNCB940', 'ACCNCB950', 'ACCNCB960', 'ACCNCB990', 'ACCNEX020', 'ACCNNN050', 'ACCNNN080', 'ACCNNN090', 'ACCNNN100', 'ACCNNN110', 'ACCNSY010', 'ACCNSY020', 'ACCNSY180', 'ACCOVL030', 'ACCOVL080', 'ACCOVL390', 'ACCOVL590', 'ACCOVL600', 'ACCOVL610', 'ACCOVL640', 'ACCOVL680', 'ACCOVL860', 'ACCPCM020', 'ACCPCM090', 'ACCPCM1160', 'ACCPCM150', 'ACCPCM170', 'ACCPCM180', 'ACCPCM200', 'ACCPCM220', 'ACCPCM480', 'ACCPTS1520', 'ACCPTS1530', 'ACCPVS040', 'ACCPVS150', 'ACCPVS160', 'ACCPVS220', 'ACCPVS240', 'ACCPVS260', 'ACCPVS360', 'ACCPVS430', 'ACCPVS440', 'ACCPVS460', 'ACCPVS530', 'ACCPVS540', 'ACCRBT2440', 'ACCRBT4560', 'ACCRBT4910', 'ACCRBT5810', 'ACCRBT6460', 'ACCRBT910', 'ACCRBT940', 'ACCSTY340', 'ACCTCH5530', 'ACCTCH8150', 'ACCTCH8180', 'ACCTND090', 'ACCTPL200', 'ACCTRG2960', 'ACCTYM020', 'ACCVGO2160', 'ACCVGO2320', 'ACCVGO760', 'ACCZBR020', 'ACPMER010', 'ACPTPL150', 'ACPTPL290', 'ACPTPL320', 'ACPTPL360', 'ACPTPL400', 'ACPTPL460', 'ACPTPL480', 'ACPUBI470', 'AIOHPI940', 'AIOHPI950', 'AIOHPI960', 'AIOHPI970', 'AIOHPI980', 'ANTUBI340', 'BATCDP020', 'BATCDP030', 'BATCDP080', 'BATDAT380', 'BATDAT410', 'BATDTS020', 'BATDTS050', 'BATDTS070', 'BATVIC040', 'BOCACT090', 'BOCACT100', 'BOCACT120', 'BOCBLR030', 'BOCGEN025', 'BOCGEN300', 'BOCGEN3300', 'BOCGEN3530', 'BOCGEN3540', 'BOCGEN3610', 'BOCGEN3620', 'BOCHIG010', 'BOCHIG030', 'BOCHIG070', 'BOCLFA090', 'BOCLNX030', 'BOCLNX090', 'BOCLOG290', 'BOCLOG830', 'BOCMST210', 'BOCMST2210', 'BOCMST2780', 'BOCMST2840', 'BOCMST2870', 'BOCMST2900', 'BOCMST3100', 'BOCNCB060', 'BOCNCB260', 'BOCNCB520', 'BOCNCB530', 'BOCNCB600', 'BOCNCB610', 'BOCNCB620', 'BOCNCB680', 'BOCNCB700', 'BOCNCB710', 'BOCNCB750', 'BOCNCB760', 'BOCNCB770', 'BOCNNN250', 'BOCNNN620', 'BOCTCH2850', 'BOCVGO1050', 'BOCVGO1320', 'BOCVGO1560', 'BOCVGO1570', 'BOCVGO1580', 'BOCVGO1590', 'BOCVGO470', 'BOCVGO560', 'CABACT1020', 'CABACT870', 'CABDAT090', 'CABDAT180', 'CABITL010', 'CABITL060', 'CABITL065', 'CABITL070', 'CABITL080', 'CABITL090', 'CABITL100', 'CABITL1025', 'CABITL1100', 'CABITL115', 'CABITL1255', 'CABITL1465', 'CABITL1520', 'CABITL170', 'CABITL1790', 'CABITL180', 'CABITL1975', 'CABITL2045', 'CABITL2330', 'CABITL255', 'CABITL260', 'CABITL265', 'CABITL275', 'CABITL280', 'CABITL300', 'CABITL370', 'CABITL3790', 'CABITL415', 'CABITL720', 'CABITL790', 'CABITL810', 'CABNCB010', 'CABNCB020', 'CABNCB390', 'CABNCB400', 'CABNCB470', 'CABUSB010', 'CABVGO490', 'CABVGO750', 'CABVGO820', 'CAMACT470', 'CAMDAH1070', 'CAMDAH1080', 'CAMDAH1090', 'CAMDAH1320', 'CAMDAH1910', 'CAMDAH2320', 'CAMDAH2630', 'CAMDAH2900', 'CAMDAH3450', 'CAMDAH3890', 'CAMEZV140', 'CAMEZV170', 'CAMEZV280', 'CAMEZV360', 'CAMEZV380', 'CAMNEX070', 'CAMPVS2580', 'CAMPVS2670', 'CAMPVS3000', 'CAMTPL190', 'CAMTPL200', 'CARBRT010', 'CARBRT1050', 'CARBRT1240', 'CARBRT1440', 'CARBRT1460', 'CARBRT2320', 'CARBRT2880', 'CARBRT3060', 'CARBRT3070', 'CARBRT3080', 'CARBRT3090', 'CARBRT3270', 'CARBRT3360', 'CARBRT3370', 'CARBRT3380', 'CARBRT340', 'CARBRT3840', 'CARBRT470', 'CARBRT480', 'CARBRT530', 'CARCNN1810', 'CARCNN4700', 'CARCNN4710', 'CARCNN4720', 'CARCNN4730', 'CAREPS2580', 'CAREPS280', 'CAREPS3900', 'CAREPS3910', 'CAREPS3920', 'CAREPS3930', 'CAREPS4010', 'CAREPS4020', 'CAREPS4030', 'CAREPS4040', 'CAREPS4050', 'CAREPS4060', 'CAREPS4370', 'CAREPS4450', 'CAREPS5570', 'CAREPS5580', 'CAREPS5590', 'CAREPS5600', 'CAREPS5820', 'CAREPS5830', 'CAREPS5840', 'CAREPS5850', 'CAREPS5910', 'CAREPS940', 'CARHPD3270', 'CARHPD3280', 'CARHPD3290', 'CARHPD3300', 'CARHPP3220', 'CARHPP3230', 'CARHPP3240', 'CARHPP4170', 'CARXRX7100', 'CARZBR1190', 'CDRASS120', 'CDRDLL040', 'CDRWCB040', 'CDRWCB125', 'CDRWCB1485', 'CDRWCB1730', 'CELHSE060', 'CJNECL060', 'CJNEVO010', 'CJNFRT030', 'COMACR8200', 'COMASS7690', 'COMDDL260', 'COMDDL650', 'COMDEL7610', 'COMDEL8720', 'COMDEL8730', 'COMGDL010', 'COMHPI2460', 'COMHPI3130', 'COMHPI3150', 'COMHPI3230', 'COMHPI3260', 'COMHPQ600', 'COMHYU200', 'COMHYU250', 'COMLEV3940', 'COMLEV4320', 'COMLNX880', 'COMMAC4080', 'COMMAC4130', 'CONLGE020', 'CPUAMD2000', 'CPUAMD2140', 'CPUAMD2270', 'CPUAMD2290', 'CPUAMD2440', 'CPUDEL4820', 'CPUDEL5220', 'CPUDEL5910', 'CPUHPI1650', 'CPUHPI1660', 'CPUHPI1680', 'CPUHPI1700', 'CPUINT3490', 'CPUINT3500', 'CPUINT3720', 'CUBKSA010', 'DDUACR010', 'DDUBLC1140', 'DDUBLC1150', 'DDUBLC1160', 'DDUDAT1020', 'DDUDAT1290', 'DDUDAT1310', 'DDUDAT1800', 'DDUDAT230', 'DDUDAT320', 'DDUDAT450', 'DDUDAT460', 'DDUDAT520', 'DDUDAT920', 'DDUGIG100', 'DDUHKV080', 'DDUHKV110', 'DDUHPO270', 'DDUHPO280', 'DDUHYU100', 'DDUKGT1290', 'DDUKGT1300', 'DDUKGT1360', 'DDUKGT2190', 'DDUKGT2200', 'DDUKGT2210', 'DDUSAN430', 'DDUSAT1090', 'DDUSAT1130', 'DDUSAT1160', 'DDUSAT250', 'DDUSAT580', 'DDUSAT830', 'DDUSAT840', 'DDUSAT960', 'DDUSGT1010', 'DDUSGT1110', 'DDUSGT1240', 'DDUSGT1290', 'DDUSGT1450', 'DDUSGT960', 'DDUTOS650', 'DDUTOS770', 'DDUTOS950', 'DDUTOS960', 'DDUTOS990', 'DDUWSD1690', 'DDUWSD1740', 'DDUWSD1850', 'DDUWSD1920', 'DDUWSD1930', 'DDUWSD2020', 'DDUXPG020', 'DDUXPG050', 'GABACT010', 'GABACT030', 'GABACT050', 'GABACT120', 'GABACT160', 'GABACT170', 'GABACT180', 'GABBLR030', 'GABEVO150', 'GABEVO170', 'GABEVO290', 'GABEVO300', 'GABEVO350', 'GABEVO360', 'GABEVO420', 'GABEVO430', 'GABEVO460', 'GABGEN125', 'GABGEN1950', 'GABGEN2200', 'GABGEN2360', 'GABGEN2560', 'GABGEN865', 'GABGEN960', 'GABNCB050', 'GABNCB090', 'GABVGO010', 'GRDDAH910', 'GRDDAH920', 'GRDDAH940', 'GRDPVS820', 'GRDPVS830', 'IMPBRT690', 'IMPCTZ140', 'IMPECL290', 'IMPEPS3410', 'IMPEPS3560', 'IMPEPS3570', 'IMPEPS3580', 'IMPEPS3590', 'IMPEPS3600', 'IMPEPS3630', 'IMPEVO010', 'IMPMTB920', 'IMPMTB930', 'IMPMTB940', 'IMPMTB950', 'IMPSMP690', 'IMPSMP710', 'IMPSMP740', 'IMPSMP770', 'IMPSMP780', 'IMPSTR880', 'KITACT1050', 'KITACT630', 'KITBLR050', 'KITBLR070', 'KITDAH480', 'KITDAH500', 'KITDAH510', 'KITDLL1160', 'KITDLL180', 'KITITL020', 'KITLOG380', 'KITLOG390', 'KITLOG410', 'KITMSF040', 'KITMST1060', 'KITMST1070', 'KITMST1080', 'KITMST1090', 'KITMST1290', 'KITNCB010', 'KITNCB020', 'KITNCB050', 'KITNCB060', 'KITNCB100', 'KITNCB110', 'KITNCB120', 'KITNCB140', 'KITNCB150', 'KITNCB160', 'KITNCB170', 'KITPVS370', 'KITTCH240', 'LCTMTR1010', 'LCTMTR1350', 'LCTMTR1770', 'LCTMTR1920', 'LCTMTR920', 'LCTTCH020', 'LCTUTC1110', 'MALDLL320', 'MALDLL490', 'MALHPI050', 'MALNCB080', 'MALNCB090', 'MBDASS5490', 'MBDASS5500', 'MBDBIO1300', 'MBDECS2170', 'MBDECS2190', 'MBDECS2220', 'MBDGIG4250', 'MBDGIG4600', 'MBDGIG4630', 'MBDGIG4730', 'MBDGIG4780', 'MBDGIG4820', 'MBDMSI1700', 'MBDMSI1740', 'MEMACR360', 'MEMACR430', 'MEMBLC1280', 'MEMBLC1380', 'MEMBLC1760', 'MEMBLC1780', 'MEMBLC1790', 'MEMBLC1810', 'MEMBLC1820', 'MEMDAT1380', 'MEMDAT1850', 'MEMDAT1880', 'MEMDAT2220', 'MEMDAT2380', 'MEMDAT2390', 'MEMDAT2400', 'MEMDAT2490', 'MEMDAT2530', 'MEMDAT2630', 'MEMDAT2640', 'MEMDAT2710', 'MEMDAT3040', 'MEMDAT3240', 'MEMDAT3250', 'MEMDAT3480', 'MEMDAT4130', 'MEMDAT4140', 'MEMDAT4340', 'MEMDAT4350', 'MEMDAT4420', 'MEMDAT4540', 'MEMDAT4700', 'MEMDAT4710', 'MEMDAT4730', 'MEMDAT4820', 'MEMDAT4830', 'MEMDAT4860', 'MEMDAT5700', 'MEMDAT5710', 'MEMDAT5760', 'MEMDAT5790', 'MEMDAT5840', 'MEMDAT5850', 'MEMDAT5890', 'MEMDAT5900', 'MEMDAT5910', 'MEMDAT5950', 'MEMDAT5960', 'MEMDAT5970', 'MEMDAT5980', 'MEMDAT6000', 'MEMDAT6030', 'MEMDAT6090', 'MEMDAT6180', 'MEMDAT6300', 'MEMDAT6350', 'MEMDAT6370', 'MEMDAT6400', 'MEMDAT6430', 'MEMDAT6500', 'MEMHKV040', 'MEMHKV050', 'MEMHKV070', 'MEMHKV090', 'MEMHKV100', 'MEMHKV110', 'MEMHKV120', 'MEMHKV170', 'MEMHKV180', 'MEMHPO090', 'MEMHYU030', 'MEMHYU050', 'MEMHYU060', 'MEMHYU070', 'MEMHYU080', 'MEMHYU090', 'MEMHYU100', 'MEMHYU110', 'MEMHYU120', 'MEMHYU130', 'MEMHYU140', 'MEMHYU390', 'MEMHYU400', 'MEMHYU410', 'MEMKGN1330', 'MEMKGN1550', 'MEMKGN1890', 'MEMKGN1900', 'MEMKGN2180', 'MEMKGN2190', 'MEMKGN2200', 'MEMKGN2210', 'MEMKGN2770', 'MEMKGN2800', 'MEMKGN2810', 'MEMKGN2840', 'MEMKGN2860', 'MEMKGN2870', 'MEMKGN2970', 'MEMKGN3080', 'MEMKGN3150', 'MEMKGN3550', 'MEMKGN3590', 'MEMKGN3640', 'MEMKGN3650', 'MEMKGN3660', 'MEMKGN3670', 'MEMKGN3750', 'MEMKGN4060', 'MEMSAN600', 'MEMSTY050', 'MEMSTY060', 'MEMSTY170', 'MEMVRT240', 'MONACR1500', 'MONACR1580', 'MONACR1600', 'MONACT010', 'MONACT020', 'MONACT030', 'MONAOC670', 'MONASS1530', 'MONBNQ1320', 'MONDLL1820', 'MONDLL2350', 'MONDLL2500', 'MONDLL2580', 'MONDLL2930', 'MONDLL3120', 'MONDLL870', 'MONDLL880', 'MONHPI440', 'MONHYU020', 'MONLGE1540', 'MONLGE1680', 'MONLGE1890', 'MONLGE2270', 'MONLGE2400', 'MONLGE2410', 'MONLGE2460', 'MONLGE2520', 'MONLGE2580', 'MONLGE2630', 'MONLNX130', 'MONNCB010', 'MONNCB020', 'MONNCB040', 'MONNCB060', 'MONNNN010', 'MONNNN040', 'MONNNN050', 'MONNNN060', 'MONNNN070', 'MONNNN080', 'MONNNN140', 'MONQIA180', 'MONQIA200', 'MONSMG1140', 'MONSTY010', 'MONVGO080', 'MONVGO150', 'MOUACT050', 'MOUACT120', 'MOUACT130', 'MOUDLL020', 'MOULOG1230', 'MOULOG1320', 'MOULOG1450', 'MOULOG1570', 'MOULOG1580', 'MOULOG1760', 'MOULOG1770', 'MOULOG2310', 'MOULOG2320', 'MOUMSF030', 'MOUMSF1390', 'MOUMST1110', 'MOUMST1130', 'MOUMST1140', 'MOUMST1150', 'MOUMST1220', 'MOUMST1430', 'MOUMST1440', 'MOUMST1510', 'MOUMST1570', 'MOUMST1580', 'MOUMST1590', 'MOUMST1600', 'MOUMST1700', 'MOUMST1710', 'MOUNCB150', 'MOUNCB260', 'MOUNCB340', 'MOUNCB350', 'MOUNCB380', 'MOUNCB400', 'MOUNCB410', 'MOUNCB420', 'MOUNCB430', 'MOUNCB440', 'MOUNCB450', 'MOUNCB460', 'MOUNCB470', 'MOUNCB480', 'MOUNCB490', 'MOUNCB500', 'MOUNCB510', 'MOUNCB520', 'MOUNCB530', 'MOUTCH250', 'MOUTCH670', 'MOUTRG070', 'MOUVGO240', 'MOUVGO480', 'MTFHPI1345', 'MTFHPI1515', 'MTFHPI1535', 'MTFHPI1545', 'NBKBIT010', 'NBKBIT020', 'NBKBIT200', 'NBKCDP1520', 'NBKCDP1530', 'NBKCOM270', 'NBKCOM290', 'NBKCYP1130', 'NBKCYP1150', 'NBKCYP1170', 'NBKCYP170', 'NBKCYP900', 'NBKCYP910', 'NBKCYP920', 'NBKSBA030', 'NBKTRL2520', 'NBKVGO040', 'NBKVIC390', 'PANHSE1120', 'PANHSE1200', 'PANHSE1280', 'PANHSE1340', 'PANHSE1350', 'PANSCR010', 'PAPXRX080', 'PAPXRX090', 'PAPXRX140', 'PROEPS2130', 'PROINF1350', 'RCKACC070', 'RCKACC1210', 'RCKACC1220', 'RCKACC1230', 'RCKACC1390', 'RCKACC1430', 'RCKACC1450', 'RCKACC1510', 'RCKACC165', 'RCKACC1700', 'RCKACC1740', 'RCKACC500', 'RCKACC510', 'RCKACC520', 'RCKACC560', 'REGBIT010', 'REGBIT030', 'REGBIT080', 'REGCDP1030', 'REGCDP1070', 'REGCOM230', 'REGCOM250', 'REGFZA050', 'REGKBZ070', 'REGKBZ200', 'REGKBZ230', 'REGKBZ240', 'REGNCB010', 'REGNSY010', 'REGSBA110', 'REGVIC070', 'RIBEVL210', 'RIBEVL800', 'ROUASS340', 'ROUMER020', 'ROUMER080', 'ROUMER090', 'ROUTND190', 'ROUTND220', 'ROUTND260', 'ROUTND430', 'ROUTPL1040', 'ROUTPL560', 'ROUTPL710', 'ROUTPL830', 'ROUTPL840', 'ROUTPL900', 'ROUTPL940', 'ROUTPL950', 'ROUTPL990', 'SCAEVO010', 'SCAEVO020', 'SERHPE1150', 'SILSTY130', 'SLXLMP020', 'SLXLMP030', 'SLXLMP040', 'SLXLMP090', 'SLXLMP120', 'SLXLMP130', 'SLXLMP150', 'SLXLMP180', 'SLXLMP200', 'SLXLMP240', 'SLXLMP250', 'SLXLMP320', 'SLXLMP370', 'SLXLMP520', 'SLXLMP640', 'SOFAVZ540', 'SOFBIT1320', 'SOFBIT1330', 'SOFBIT1340', 'SOFBIT1370', 'SOFBIT1400', 'SOFBIT890', 'SOFBIT900', 'SOFEST2040', 'SOFEST2050', 'SOFEST2060', 'SOFEST2130', 'SOFEST2150', 'SOFEST3220', 'SOFING310', 'SOFMSC1410', 'SOFMSC1440', 'SOFMSC1450', 'SUPBIT010', 'SUPKBZ140', 'SWTDAH150', 'SWTLNK1100', 'SWTMER010', 'SWTMER020', 'SWTMER030', 'SWTMER040', 'SWTTPL010', 'SWTTPL020', 'SWTTPL120', 'SWTTPL130', 'SWTTPL140', 'SWTTPL720', 'SWTTPL730', 'SWTTPL750', 'SWTTPL760', 'TABACT010', 'TABLEN1150', 'TABLEN1160', 'TABLEN1170', 'TABLEN1180', 'TABLNX410', 'TABLNX420', 'TABSMG170', 'TABSMG210', 'TABSMG220', 'TARITL710', 'TARMER020', 'TARTPL180', 'TARTPL200', 'TARTPL370', 'TARTPL380', 'TARTPL410', 'TARTPL440', 'TARTPL580', 'TARTPL590', 'TECACT060', 'TECACT120', 'TECDLL080', 'TECHPI030', 'TECLOG250', 'TECMSF190', 'TECMST820', 'TECMST950', 'TECMST990', 'TECNCB030', 'TECNCB040', 'TECTCH070', 'TELGDM190', 'TELPAN080', 'TELPAN085', 'TELPAN090', 'TELPAN095', 'TELPAN100', 'TELPTS010', 'TERKSA020', 'TLCHYU330', 'TONHPP3710', 'TONHPP3730', 'TONHPP4260', 'TVIASS2990', 'TVIGIG2090', 'TVIGIG2800', 'VENBLR020', 'VENITL280', 'VENNCB020', 'VENNCB040', 'VENNCB050', 'VENNCB080', 'VENNCB090', 'VENNCB100', 'VENNCB110'])
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

}