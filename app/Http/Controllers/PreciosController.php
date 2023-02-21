<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\PreciosAbasteoController;
use App\Http\Controllers\CyberPuertaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Categoria;
use App\Models\imagenProducto;
use App\Models\Marca;
use App\Models\Palabras;
use App\Models\Subcategoria;
use App\Models\Producto;
use App\Models\Promocion;
use Illuminate\Support\Facades\Storage;
use Goutte\Client AS Client2; 

class PreciosController extends Controller
{
    public function index()
    {
        $data['categorias'] = Categoria::distinct('nombre')->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['productos'] = '';
        return view('filtros', compact('data'));
    }

    public function getCategorias($id = null)
    {
        // $data = Subcategoria::distinct('nombre')->where('categoria_id',$id)->get();
        // return response()->json($data);
        $sql = "select id,nombre from subcategorias where id IN(select DISTINCT subcategoria_id from productos where categoria_id = ? and estatus = 'Activo') order by nombre asc";
        $data = DB::select($sql, array($id));
        return response()->json($data);
    }

    public function getMarcas($id = null, $id2 = null)
    {
        $sql = "select id,nombre from marcas where id IN(select DISTINCT marca_id from productos where categoria_id = ? and subcategoria_id = ? and estatus = 'Activo') order by nombre asc";
        $data = DB::select($sql, array($id, $id2));
        return response()->json($data);
    }

    public function cotizar(Request $request)
    {
        $preciosAbasteo = new PreciosAbasteoController;
        // $preciosCyberpuerta = new CyberPuertaController;
        $preciosMiPC = new MiPCController;
        $preciosZegucom = new ZegucomController;
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $test3 = $request->get('filtro3');
        if ($test3 == 'z') {
            $data['productos'] = Producto::where('categoria_id', $test)->where('subcategoria_id', $test2)->where('estatus', 'Activo')->get();
        } else {
            $data['productos'] = Producto::where('categoria_id', $test)->where('subcategoria_id', $test2)->where('marca_id', $test3)->where('estatus', 'Activo')->get();
        }
        if (sizeof($data['productos']) > 0) {
            $data['abasteo'] = $preciosAbasteo->cotizar($data['productos']);
            // $data['cyberpuerta'] = $preciosCyberpuerta->cotizar($data['productos']);
            $data['mipc'] = $preciosMiPC->cotizar($data['productos']);
            $data['zegucom'] = $preciosZegucom->cotizar($data['productos']);
            $data['categoria'] = $request->get('filtro1');
            $data['subcategoria'] = $request->get('filtro2');
            // $existencias = new CTConnect;
            // $data['existencias'] = $existencias->existencias($data['productos']);
        }
        $data['categorias'] = Categoria::distinct('nombre')->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        return view('filtros', compact('data'));
    }

    public function lectura()
    {
        $products = new ProductosController();
        $existencia_producto = 0;
        // $products->limpieza();
        set_time_limit(0);
        $fileExist = Storage::disk('prueba-ftp')->exists('catalogo_xml/productos.json');
        if ($fileExist) {
            Storage::disk('local')->put('public/products.json', Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
        } else {
            dd("No existe");
        }
        //$productos = Storage::get('public/products.json');
        //     set_time_limit(0);
        //     for($i=0;$i<sizeof($productos);$i++){
        //         if($productos[$i]['idCategoria']!=0){

        //             $marca_nueva = Marca::updateOrCreate(
        //                 ['id'=>$productos[$i]['idMarca']],
        //                 [
        //                     'id'=>$productos[$i]['idMarca'],
        //                     'nombre'=>$productos[$i]['marca']
        //                 ]
        //             );
        //             $categoria_nueva = Categoria::updateOrCreate(
        //                 ['id'=>$productos[$i]['idCategoria']],
        //                 [
        //                     'id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['categoria']
        //                 ]
        //             );
        //             $subcategoria_nueva = Subcategoria::updateOrCreate(
        //                 ['id'=>$productos[$i]['idSubCategoria']],
        //                 [
        //                     'id'=>$productos[$i]['idSubCategoria'],
        //                     'categoria_id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['subcategoria']
        //                 ]
        //             );
        //             $producto = Producto::updateOrCreate(
        //                 ['clave_ct'=>$productos[$i]['clave']],
        //                 [
        //                     'marca_id'=>$productos[$i]['idMarca'],
        //                     'subcategoria_id'=>$productos[$i]['idSubCategoria'],
        //                     'categoria_id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['nombre'],
        //                     'descripcion_corta'=>$productos[$i]['descripcion_corta'],
        //                     'precio_unitario'=>$productos[$i]['moneda'] == "USD" ? number_format((($productos[$i]['precio']*$productos[$i]['tipoCambio'])*1.16),2,'.',''):number_format(($productos[$i]['precio']*1.16),2,'.',''),
        //                     'sku'=>ltrim($productos[$i]['numParte']),
        //                     'ean'=>$productos[$i]['ean'],
        //                     'upc'=>$productos[$i]['upc'],
        //                     'imagen'=>$productos[$i]['imagen'],
        //                     'existencias'=>$existencia_producto,
        //                     'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
        //                 ]
        //             );
        //             if(!empty($productos[$i]['promociones'])){
        //                 // dd($productos[$i]['promociones'][0]['vigencia']['inicio']);
        //                 // date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio']));
        //                 // date('Y-m-d\TH:i:s', $productos[$i]['promociones'][0]['vigencia']['inicio']);
        //                 if($productos[$i]['promociones'][0]['tipo']!="porcentaje"){
        //                     $promocion = Promocion::updateOrCreate(
        //                         ['clave_ct'=>$productos[$i]['clave']],
        //                         ['descuento'=>100-($productos[$i]['promociones'][0]['promocion']*100)/$productos[$i]['precio'],
        //                         'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
        //                         'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
        //                     );
        //                 }else{
        //                     $promocion = Promocion::updateOrCreate(
        //                         ['clave_ct'=>$productos[$i]['clave']],
        //                         ['descuento'=>$productos[$i]['promociones'][0]['promocion'],
        //                         'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
        //                         'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
        //                     );
        //                 }
        //                 // dd($productos[$i]['clave']);
        //             }
        //             $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
        //             for($j=0;$j<sizeof($palabras_clave);$j++){
        //                 $producto = Palabras::updateOrCreate(
        //                     ['clave_ct'=>$productos[$i]['clave'],
        //                     'palabra'=>$palabras_clave[$j]]
        //                 );
        //             }
        //         }
        //     }
        //     dd($productos);
    }

    public function lecturaLocal()
    {
        set_time_limit(0);
        $products = new ProductosController();
        $imagenes = new ImagenesController();
        $existencia_producto = 0;
        //$products->limpieza();
        // $productos = storage_path() . "/app/public/productos.json";
        $productos = json_decode(file_get_contents(storage_path() . "/app/public/productos.json"), true);
        // dd(filesize($productos));
        // dd(storage_path() . "/app/public/productos.json");
        //dd($productos);
        if(date('d')==01){
            $borrarProm = Promocion::where('id','>',0)->delete();
            dd('Tabla Borrada');
        }
        for ($i = 0; $i < sizeof($productos); $i++) {
            // for($i=0;$i<3;$i++){
            $existencia_producto = 0;
            if ($productos[$i]['idCategoria'] != 0) {
                // if($i>=0){
                //     $imagenes->obtener($productos[$i]);
                // }
                // PRUEBA EXISTENCIAS
                $existencia_producto = $this->existencias($productos[$i]);
                $marca_nueva = Marca::updateOrCreate(
                    ['id' => $productos[$i]['idMarca']],
                    [
                        'id' => $productos[$i]['idMarca'],
                        'nombre' => $productos[$i]['marca']
                    ]
                );
                $categoria_nueva = Categoria::updateOrCreate(
                    ['id' => $productos[$i]['idCategoria']],
                    [
                        'id' => $productos[$i]['idCategoria'],
                        'nombre' => $productos[$i]['categoria']
                    ]
                );
                $subcategoria_nueva = Subcategoria::updateOrCreate(
                    ['id' => $productos[$i]['idSubCategoria']],
                    [
                        'id' => $productos[$i]['idSubCategoria'],
                        'categoria_id' => $productos[$i]['idCategoria'],
                        'nombre' => $productos[$i]['subcategoria']
                    ]
                );
                $producto = Producto::updateOrCreate(
                    ['clave_ct' => $productos[$i]['clave']],
                    [
                        'marca_id' => $productos[$i]['idMarca'],
                        'subcategoria_id' => $productos[$i]['idSubCategoria'],
                        'categoria_id' => $productos[$i]['idCategoria'],
                        'nombre' => $productos[$i]['nombre'],
                        'descripcion_corta' => $productos[$i]['descripcion_corta'],
                        'precio_unitario' => $productos[$i]['moneda'] == "USD" ? number_format((($productos[$i]['precio'] * $productos[$i]['tipoCambio']) * 1.16), 2, '.', '') : number_format(($productos[$i]['precio'] * 1.16), 2, '.', ''),
                        'sku' => ltrim($productos[$i]['numParte']),
                        'ean' => $productos[$i]['ean'],
                        'upc' => $productos[$i]['upc'],
                        'imagen' => $productos[$i]['imagen'],
                        'existencias' => $existencia_producto,
                        'estatus' => $productos[$i]['activo'] == 1 ? 'Activo' : 'Descontinuado'
                    ]
                );
                if (!empty($productos[$i]['promociones'])) {
                    // dd($productos[$i]['promociones'][0]['vigencia']['inicio']);
                    // date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio']));
                    // date('Y-m-d\TH:i:s', $productos[$i]['promociones'][0]['vigencia']['inicio']);
                    if ($productos[$i]['promociones'][0]['tipo'] != "porcentaje") {
                        $promocion = Promocion::updateOrCreate(
                            ['clave_ct' => $productos[$i]['clave']],
                            [
                                'descuento' => 100 - ($productos[$i]['promociones'][0]['promocion'] * 100) / $productos[$i]['precio'],
                                'fecha_inicio' => date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
                                'fecha_fin' => date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))
                            ]
                        );
                    } else {
                        $promocion = Promocion::updateOrCreate(
                            ['clave_ct' => $productos[$i]['clave']],
                            [
                                'descuento' => $productos[$i]['promociones'][0]['promocion'],
                                'fecha_inicio' => date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
                                'fecha_fin' => date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))
                            ]

                        );
                    }
                }
                // $url = $productos[$i]['imagen'];
                // $contents = file_get_contents($url);
                // $datos = pathinfo($url);
                // $nombre = $productos[$i]['numParte']."-0.".$datos['extension'];
                // list($width, $height, $type, $attr) = getimagesize($url);
                // if($productos[$i]['clave']!="CAMDAH3650"){
                //     $imagenes = imagenProducto::updateOrCreate(
                //         ['clave_ct'=>$productos[$i]['clave'],
                //         'imagen'=>$nombre],
                //         ['largo'=>$width,
                //         'ancho'=>$height]
                //     );
                // }
                $palabras_clave = explode(",", $productos[$i]['descripcion_corta']);
                for ($j = 0; $j < sizeof($palabras_clave); $j++) {
                    $producto = Palabras::updateOrCreate(
                        [
                            'clave_ct' => $productos[$i]['clave'],
                            'palabra' => $palabras_clave[$j]
                        ]
                    );
                }
                // $existencias = new CTConnect;
                // $existencias->existencias($productos);
            }
        }
        // $existencias = new CTConnect;
        //         $existencias->existencias($productos);
        // for($i=0;$i<2;$i++){
        //     if($productos[$i]['idCategoria']!=0){
        //         $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
        //         for($j=0;$j<sizeof($palabras_clave);$j++){
        //             $producto = Palabras::updateOrCreate(
        //                 ['clave_ct'=>$productos[$i]['clave'],
        //                 'palabra'=>$palabras_clave[$j]]
        //             );
        //         }
        //     }
        // }
        // dd($productos);
        dd("Archivo cargado");
    }

    public function existencias($productos)
    {
        set_time_limit(0);
        //dd($productos);
        $existencia_producto = 0;
        if (!empty($productos['existencia']['DFA'])) {
            $existencia_producto += $productos['existencia']['DFA'];
        }
        if (!empty($productos['existencia']['D2A'])) {
            $existencia_producto += $productos['existencia']['D2A'];
        }
        if (!empty($productos['existencia']['CAM'])) {
            $existencia_producto += $productos['existencia']['CAM'];
        }
        if (!empty($productos['existencia']['GDL'])) {
            $existencia_producto += $productos['existencia']['GDL'];
            // dd($productos['existencia']['GDL'].$productos['clave']);
        }
        if (!empty($productos['existencia']['ZAC'])) {
            $existencia_producto += $productos['existencia']['ZAC'];
        }
        if (!empty($productos['existencia']['ACA'])) {
            $existencia_producto += $productos['existencia']['ACA'];
        }
        if (!empty($productos['existencia']['QRO'])) {
            $existencia_producto += $productos['existencia']['QRO'];
        }
        if (!empty($productos['existencia']['COL'])) {
            $existencia_producto += $productos['existencia']['COL'];
        }
        if (!empty($productos['existencia']['HMO'])) {
            $existencia_producto += $productos['existencia']['HMO'];
        }
        if (!empty($productos['existencia']['LMO'])) {
            $existencia_producto += $productos['existencia']['LMO'];
        }
        if (!empty($productos['existencia']['CLN'])) {
            $existencia_producto += $productos['existencia']['CLN'];
        }
        if (!empty($productos['existencia']['CHI'])) {
            $existencia_producto += $productos['existencia']['CHI'];
        }
        if (!empty($productos['existencia']['MOR'])) {
            $existencia_producto += $productos['existencia']['MOR'];
        }
        if (!empty($productos['existencia']['VER'])) {
            $existencia_producto += $productos['existencia']['VER'];
        }
        if (!empty($productos['existencia']['CTZ'])) {
            $existencia_producto += $productos['existencia']['CTZ'];
        }
        if (!empty($productos['existencia']['TAM'])) {
            $existencia_producto += $productos['existencia']['TAM'];
        }
        if (!empty($productos['existencia']['PUE'])) {
            $existencia_producto += $productos['existencia']['PUE'];
        }
        if (!empty($productos['existencia']['VHA'])) {
            $existencia_producto += $productos['existencia']['VHA'];
        }
        if (!empty($productos['existencia']['TUX'])) {
            $existencia_producto += $productos['existencia']['TUX'];
        }
        if (!empty($productos['existencia']['MTY'])) {
            $existencia_producto += $productos['existencia']['MTY'];
        }
        if (!empty($productos['existencia']['MID'])) {
            $existencia_producto += $productos['existencia']['MID'];
        }
        if (!empty($productos['existencia']['MAZ'])) {
            $existencia_producto += $productos['existencia']['MAZ'];
        }
        if (!empty($productos['existencia']['CUE'])) {
            $existencia_producto += $productos['existencia']['CUE'];
        }
        if (!empty($productos['existencia']['CUN'])) {
            $existencia_producto += $productos['existencia']['CUN'];
        }
        if (!empty($productos['existencia']['DFP'])) {
            $existencia_producto += $productos['existencia']['DFP'];
        }
        if (!empty($productos['existencia']['ACX'])) {
            $existencia_producto += $productos['existencia']['ACX'];
        }
        if (!empty($productos['existencia']['CEL'])) {
            $existencia_producto += $productos['existencia']['CEL'];
        }
        if (!empty($productos['existencia']['OBR'])) {
            $existencia_producto += $productos['existencia']['OBR'];
        }
        if (!empty($productos['existencia']['DGO'])) {
            $existencia_producto += $productos['existencia']['DGO'];
        }
        if (!empty($productos['existencia']['TRN'])) {
            $existencia_producto += $productos['existencia']['TRN'];
        }
        if (!empty($productos['existencia']['AGS'])) {
            $existencia_producto += $productos['existencia']['AGS'];
        }
        if (!empty($productos['existencia']['SLP'])) {
            $existencia_producto += $productos['existencia']['SLP'];
        }
        if (!empty($productos['existencia']['XLP'])) {
            $existencia_producto += $productos['existencia']['XLP'];
        }
        if (!empty($productos['existencia']['DFT'])) {
            $existencia_producto += $productos['existencia']['DFT'];
        }
        if (!empty($productos['existencia']['CDV'])) {
            $existencia_producto += $productos['existencia']['CDV'];
        }
        if (!empty($productos['existencia']['SLT'])) {
            $existencia_producto += $productos['existencia']['SLT'];
        }
        if (!empty($productos['existencia']['TPC'])) {
            $existencia_producto += $productos['existencia']['TPC'];
        }
        if (!empty($productos['existencia']['TOL'])) {
            $existencia_producto += $productos['existencia']['TOL'];
        }
        if (!empty($productos['existencia']['PAC'])) {
            $existencia_producto += $productos['existencia']['PAC'];
        }
        if (!empty($productos['existencia']['IRA'])) {
            $existencia_producto += $productos['existencia']['IRA'];
        }
        if (!empty($productos['existencia']['OAX'])) {
            $existencia_producto += $productos['existencia']['OAX'];
        }
        if (!empty($productos['existencia']['DFC'])) {
            $existencia_producto += $productos['existencia']['DFC'];
        }
        if (!empty($productos['existencia']['TXL'])) {
            $existencia_producto += $productos['existencia']['TXL'];
        }
        if (!empty($productos['existencia']['URP'])) {
            $existencia_producto += $productos['existencia']['URP'];
        }
        //dd($existencia_producto);
        return $existencia_producto;
    }

    public function lecturaPrueba()
    {
        $products = new ProductosController();
        $existencia_producto = 0;
        // $products->limpieza();
        set_time_limit(0);
        $fileExist = Storage::disk('prueba-ftp')->exists('catalogo_xml/productos.json');
        if ($fileExist) {
            $size = storage_path('catalogo_xml/productos.json');
            //Storage::disk('local')->put('public/products.json',Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
            Storage::disk('local')->put('public/products-' . filesize($size) . '.json', Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
        } else {
            dd("No existe");
        }
        //     set_time_limit(0);
        //     for($i=0;$i<sizeof($productos);$i++){
        //         if($productos[$i]['idCategoria']!=0){

        //             $marca_nueva = Marca::updateOrCreate(
        //                 ['id'=>$productos[$i]['idMarca']],
        //                 [
        //                     'id'=>$productos[$i]['idMarca'],
        //                     'nombre'=>$productos[$i]['marca']
        //                 ]
        //             );
        //             $categoria_nueva = Categoria::updateOrCreate(
        //                 ['id'=>$productos[$i]['idCategoria']],
        //                 [
        //                     'id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['categoria']
        //                 ]
        //             );
        //             $subcategoria_nueva = Subcategoria::updateOrCreate(
        //                 ['id'=>$productos[$i]['idSubCategoria']],
        //                 [
        //                     'id'=>$productos[$i]['idSubCategoria'],
        //                     'categoria_id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['subcategoria']
        //                 ]
        //             );
        //             $producto = Producto::updateOrCreate(
        //                 ['clave_ct'=>$productos[$i]['clave']],
        //                 [
        //                     'marca_id'=>$productos[$i]['idMarca'],
        //                     'subcategoria_id'=>$productos[$i]['idSubCategoria'],
        //                     'categoria_id'=>$productos[$i]['idCategoria'],
        //                     'nombre'=>$productos[$i]['nombre'],
        //                     'descripcion_corta'=>$productos[$i]['descripcion_corta'],
        //                     'precio_unitario'=>$productos[$i]['moneda'] == "USD" ? number_format((($productos[$i]['precio']*$productos[$i]['tipoCambio'])*1.16),2,'.',''):number_format(($productos[$i]['precio']*1.16),2,'.',''),
        //                     'sku'=>ltrim($productos[$i]['numParte']),
        //                     'ean'=>$productos[$i]['ean'],
        //                     'upc'=>$productos[$i]['upc'],
        //                     'imagen'=>$productos[$i]['imagen'],
        //                     'existencias'=>$existencia_producto,
        //                     'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
        //                 ]
        //             );
        //             if(!empty($productos[$i]['promociones'])){
        //                 // dd($productos[$i]['promociones'][0]['vigencia']['inicio']);
        //                 // date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio']));
        //                 // date('Y-m-d\TH:i:s', $productos[$i]['promociones'][0]['vigencia']['inicio']);
        //                 if($productos[$i]['promociones'][0]['tipo']!="porcentaje"){
        //                     $promocion = Promocion::updateOrCreate(
        //                         ['clave_ct'=>$productos[$i]['clave']],
        //                         ['descuento'=>100-($productos[$i]['promociones'][0]['promocion']*100)/$productos[$i]['precio'],
        //                         'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
        //                         'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
        //                     );
        //                 }else{
        //                     $promocion = Promocion::updateOrCreate(
        //                         ['clave_ct'=>$productos[$i]['clave']],
        //                         ['descuento'=>$productos[$i]['promociones'][0]['promocion'],
        //                         'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
        //                         'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
        //                     );
        //                 }
        //                 // dd($productos[$i]['clave']);
        //             }
        //             $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
        //             for($j=0;$j<sizeof($palabras_clave);$j++){
        //                 $producto = Palabras::updateOrCreate(
        //                     ['clave_ct'=>$productos[$i]['clave'],
        //                     'palabra'=>$palabras_clave[$j]]
        //                 );
        //             }
        //         }
        //     }
        //     dd($productos);
    }

    public function sitemap(){
        set_time_limit(0);
        $texto1 = '<?xml version="1.0" encoding="utf-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
          <url>
            <loc>https://ehstecnologias.com.mx</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/bocinas-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/controles-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/diademas-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/escritorio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/fuentes-de-poder-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/gabinetes-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/kits-de-teclado-y-mouse-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/mochila-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/motherboards-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/mouse-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/mouse-pads-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/sillas-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/tarjetas-de-video-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-gaming/teclados-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/accesorios-para-pcs</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/adaptadores-para-disco-duro</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/adaptadores-usb</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/ergonomia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/herramientas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/kits-para-teclado-y-mouse</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/mouse</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/mouse-pads</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/soportes-para-pcs</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/teclados</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-componentes/webcams</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/accesorios-para-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/adaptadores-para-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/bases-enfriadoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/baterias-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/candados-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/docking-station</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/extension-de-garantias</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/filtro-de-privacidad</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/fundas-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/fundas-para-tablets</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/mochilas-y-maletines</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/pantallas-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/protectores-para-tablets</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-computo/teclados-laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/accesorios-para-camaras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/accesorios-para-celulares</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/audifonos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/cargadores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/controles</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/diademas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/diademas-y-audifonos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/equipo-para-celulares</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/fundas-y-maletines</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/lentes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/limpieza</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/pilas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/plumas-interactivas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/power-banks</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-electronica/soportes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-energia/adaptadores-de-energia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-energia/iluminacion</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-impresion/accesorios-para-impresoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-impresion/gabinetes-para-impresoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-para-impresion/mantenimiento</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/bases</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/baterias-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/cables-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/etiquetas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/garantias</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/accesorios-y-consumibles-pos/torretas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-de-ethernet</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-inalambricos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-para-audio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-para-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-para-video</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/adaptadores-usb-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/amplificadores-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/antenas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/concentradores-hub</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/convertidor-de-medios</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/tarjetas-para-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/adaptadores/transceptores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento/almacenamiento-externo</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento/discos-duros</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento/ssd</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/almacenamiento-optico</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/discos-duros-externos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/gabinetes-para-discos-duros</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/memorias-flash</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/almacenamiento-portatil/memorias-usb</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/accesorios-para-apple</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/adaptadores-para-apple</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/audifonos-para-apple</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/cables-lightning</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/imac</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/ipad</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/macbook</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/apple/perifericos-apple</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/bocinas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/home-theaters</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/microfonos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/micro-y-mini-componentes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/audio/reproductores-mp3</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/baterias-banks/bancos-de-bateria</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/baterias-banks/reemplazos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/accesorios-para-cables</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-displayport</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-dvi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-hdmi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-usb-para-video</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/adaptadores-vga</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/bobinas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-alimentacion</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-audio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-energia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-de-video</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-displayport</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-dvi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-hdmi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-kvm</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-serial</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-usb</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/cables-vga</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/convertidores-av</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/herramientas-para-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/cables/jacks</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/all-in-one</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/laptops</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/mini-pc</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/pcs-de-escritorio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras/tabletas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras-gaming/laptops-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras-gaming/monitores-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/computadoras-gaming/pcs-de-escritorio-gaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/cabezales</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/cartuchos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/cintas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/papeleria</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/refacciones</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/consumibles/toners</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/credencializacion/consumibles-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/credencializacion/digitalizadores-de-firmas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/digitalizacion-de-imagenes/escaner</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/contactos-inteligentes-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/control-inteligente</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/hub-y-concentadores-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/interruptores-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/domotica/sensores-wifi</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/auricurales</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/camaras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/celulares</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/consolas-y-video-juegos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/pantallas-de-proyeccion</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/proyectores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/reproductores-dvd-y-blu-ray</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/smartwatch</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/streaming</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/televisiones</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/electronica/transmisores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/energia/baterias</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/energia-solar-y-eolica/inversores-de-energia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/enfriamiento-y-ventilacion</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/fuentes-de-poder</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/gabinetes-para-computadoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/lectores-de-memorias</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/memorias-ram</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/microprocesadores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/monitores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/motherboards</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/ensamble/quemadores-dvd-y-bluray</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/esd/licencias-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/impresion/impresoras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/impresion/multifuncionales</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/impresion/plotters</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/impresion/rotuladores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/licenciamiento/sistemas-operativos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/modulos-supresores/estaciones-de-carga</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/modulos-supresores/regletas-y-multicontactos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/modulos-supresores/supresores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/modulos-supresores/transformadores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/cajones-de-dinero</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/impresoras-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/kit-punto-de-venta</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/lectores-de-codigos-de-barras</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/monitores-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/perifericos-para-pos/terminales-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/productividad/fpp</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-activa/access-points</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-activa/extensores-de-red</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-activa/routers</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-activa/switches</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/redes/accesorios-de-redes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/redes/networking</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-pasiva/accesorios-para-racks</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-pasiva/gabinetes-de-piso</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-pasiva/gabinetes-para-montaje</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/red-pasiva/racks-modulo</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/respaldo-y-regulacion/no-breaks-y-ups</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/respaldo-y-regulacion/reguladores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/caretas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/cubrebocas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/desinfectantes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/equipo</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/tapetes</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/salud/termometros</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/seguridad/antivirus</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/seguridad/videovigilancia</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/seguridad-electronica/corporativos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/seguridad-electronica/pequenos-negocios</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/sistema-para-puntos-de-venta/software-pos</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/sistemas-operativos/windows</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/software-administrativo/licencias-microsoft</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/software-pos/licencias-cisco</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/solucion-para-servidores/almacenamiento-nas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/solucion-para-servidores/gabinete-para-almacenaje</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/solucion-para-servidores/servidores</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/tarjetas/tarjetas-de-sonido</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/tarjetas/tarjetas-de-video</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/tarjetas/tarjetas-paralelas</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/tarjetas/tarjetas-seriales</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <url>
            <loc>https://ehstecnologias.com.mx/productos/workstations/workstations-de-escritorio</loc>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
          </url>
          <!-- PRODUCTOS ACTIVOS -->';
        Storage::put('sitemapE.xml', $texto1);
        $remove = array(" ", "  ", "   ", "    ", "(", ")", "$", "*", "/", ",", "IVA", "Incluido");
        $client = new Client2();
        //dd('noexiste');
        $productos = Producto::where('estatus','=','Activo')
        //$productos = Producto::whereNull('enlace')
        //->where('estatus','=','Activo')
        //->orderBy('enlace')
        ->get([
            'enlace',
            'sku',
            'clave_ct'
        ]);
        //dd($productos[0]);
        for ($i = 0; $i < sizeof($productos); $i++) {
        $texto2 = '';
        //for ($i = 0; $i < 5; $i++) {
            if(($productos[$i]->enlace) == null){
                $cliente = new Client2();
                $sku = $productos[$i]->sku;
                $clavect = $productos[$i]->clave_ct;
                //dd($sku);
                if($sku==""){
                    $sku="NO EXISTE";
                }
                $website2 = $cliente->request('GET', 'https://ehstecnologias.com.mx/productos?b=' . $sku);
                //$website2 = $cliente->request('GET', 'https://ctonline.mx/buscar/productos?b=' . $sku);
                //dd('https://ctonline.mx/buscar/productos?b=' . $sku);
                //$resultado = $website2->filter('.imagen_centrica > a');
                //dd($website2->filter('.content-img > a')->text());
                if ($website2->filter('.content-img > a')->count()>0){
                  $resultado = $website2->filter('.content-img > a');
                  $texto2 = "<url><loc>".$resultado->attr('href')."</loc><changefreq>daily</changefreq><priority>0.5</priority></url>";
                  //dd($texto2);
                  $enlaces = Producto::updateOrCreate(
                    ['clave_ct' => $clavect],
                    [
                      'enlace' => $resultado->attr('href'),
                    ]
                  )
                  ->where('clave_ct', '=', $clavect)
                  ->where('sku', '=', $sku);
                  Storage::append("sitemapE.xml", $texto2);
                }else{
                  //dd('Producto No Encontrado');
                }
                //dd($resultado->attr('href'));
            }else{
                $enlace = $productos[$i]->enlace;
                //dd($enlace);
                $texto2 = " <url><loc>".$enlace."</loc><changefreq>daily</changefreq><priority>0.5</priority></url>";
                Storage::append("sitemapE.xml", $texto2);
            }
            }
        $texto3 = "</urlset>";
        Storage::append("sitemapE.xml", $texto3);
        dd('Sitemap Creado');
  }

  public function enlaces(){
    set_time_limit(0);
    $remove = array(" ", "  ", "   ", "    ", "(", ")", "$", "*", "/", ",", "IVA", "Incluido");
    $client = new Client2();
    //dd('noexiste');
    //$productos = Producto::where('estatus','=','Activo')
    $productos = Producto::whereNull('enlace')
    ->where('estatus','=','Activo')
    //->orderBy('enlace')
    ->get([
      'enlace',
      'sku',
      'clave_ct'
      ]);
    for ($i = 0; $i < sizeof($productos); $i++) {
    //for ($i = 0; $i < 5; $i++) {
      if(($productos[$i]->enlace) == null){
        $cliente = new Client2();
        //$sku = $productos[$i]->sku;
        $sku = 'ES-05002';
        //dd($sku);
        if($sku==""){
          $sku="NO EXISTE";
        }
        $website2 = $cliente->request('GET', 'https://ehstecnologias.com.mx/productos?b=' . $sku);
        //$website2 = $cliente->request('GET', 'https://ctonline.mx/buscar/productos?b=' . $sku);
        //dd('https://ctonline.mx/buscar/productos?b=' . $sku);
        //$resultado = $website2->filter('.imagen_centrica > a');
        //dd($website2->filter('.content-img > a')->text());
        $clavect = $productos[$i]->clave_ct;
        if ($website2->filter('.content-img > a')->count()>0){
          $resultado = $website2->filter('.content-img > a');
          //dd($resultado->attr('href'));
          $enlaces = Producto::updateOrCreate(
            ['clave_ct' => $clavect],
            [
              'enlace' => $resultado->attr('href'),
            ]
          )
          ->where('clave_ct', '=', $clavect)
          ->where('sku', '=', $sku)
          
          ;
        }else{
          //dd('Producto No Encontrado');
        }
        //dd($resultado->attr('href'));
      }else{
      }
    }
    dd('Enlace Actualizado');
  }
}

