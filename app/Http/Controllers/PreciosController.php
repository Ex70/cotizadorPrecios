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

class PreciosController extends Controller{
    public function index(){
        $data['categorias'] = Categoria::distinct('nombre')->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['productos'] = '';
        return view('filtros',compact('data'));
    }

    public function getCategorias($id = null){
        // $data = Subcategoria::distinct('nombre')->where('categoria_id',$id)->get();
        // return response()->json($data);
        $sql = "select id,nombre from subcategorias where id IN(select DISTINCT subcategoria_id from productos where categoria_id = ? and estatus = 'Activo') order by nombre asc";
        $data = DB::select($sql,array($id));
        return response()->json($data);
    }

    public function getMarcas($id = null,$id2 = null){
        $sql = "select id,nombre from marcas where id IN(select DISTINCT marca_id from productos where categoria_id = ? and subcategoria_id = ? and estatus = 'Activo') order by nombre asc";
        $data = DB::select($sql,array($id,$id2));
        return response()->json($data);
    }

    public function cotizar(Request $request){
        $preciosAbasteo = new PreciosAbasteoController;
        // $preciosCyberpuerta = new CyberPuertaController;
        $preciosMiPC = new MiPCController;
        $preciosZegucom = new ZegucomController;
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $test3 = $request->get('filtro3');
        if($test3 == 'z'){
            $data['productos'] = Producto::where('categoria_id',$test)->where('subcategoria_id',$test2)->where('estatus','Activo')->get();
        }else{
            $data['productos'] = Producto::where('categoria_id',$test)->where('subcategoria_id',$test2)->where('marca_id',$test3)->where('estatus','Activo')->get();
        }
        if(sizeof($data['productos'])>0){
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
        return view('filtros',compact('data'));
    }

    public function lectura(){
        $products = new ProductosController();
        $existencia_producto=0;
        $products->limpieza();
        set_time_limit(0);
        $fileExist = Storage::disk('prueba-ftp')->exists('catalogo_xml/productos.json');
        if ($fileExist){
            Storage::disk('local')->put('public/products.json',Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
        } else{
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

    public function lecturaLocal (){
        $products = new ProductosController();
        $imagenes = new ImagenesController();
        $existencia_producto=0;
        // $products->limpieza();
        
        $productos = storage_path() . "/app/public/productos.json";
        dd(filesize($productos));// dd(storage_path() . "/app/public/productos.json");
        //dd($productos);
        set_time_limit(0);
        //for($i=0;$i<sizeof($productos);$i++){
        for($i=0;$i<3;$i++){
            $existencia_producto=0;
            if($productos[$i]['idCategoria']!=0){
                // if($i>=0){
                //     $imagenes->obtener($productos[$i]);
                // }
                // PRUEBA EXISTENCIAS
                $existencia_producto = $this->existencias($productos[$i]);
                $marca_nueva = Marca::updateOrCreate(
                    ['id'=>$productos[$i]['idMarca']],
                    [
                        'id'=>$productos[$i]['idMarca'],
                        'nombre'=>$productos[$i]['marca']
                    ]
                );
                $categoria_nueva = Categoria::updateOrCreate(
                    ['id'=>$productos[$i]['idCategoria']],
                    [
                        'id'=>$productos[$i]['idCategoria'],
                        'nombre'=>$productos[$i]['categoria']
                    ]
                );
                $subcategoria_nueva = Subcategoria::updateOrCreate(
                    ['id'=>$productos[$i]['idSubCategoria']],
                    [
                        'id'=>$productos[$i]['idSubCategoria'],
                        'categoria_id'=>$productos[$i]['idCategoria'],
                        'nombre'=>$productos[$i]['subcategoria']
                    ]
                );
                $producto = Producto::updateOrCreate(
                    ['clave_ct'=>$productos[$i]['clave']],
                    [
                        'marca_id'=>$productos[$i]['idMarca'],
                        'subcategoria_id'=>$productos[$i]['idSubCategoria'],
                        'categoria_id'=>$productos[$i]['idCategoria'],
                        'nombre'=>$productos[$i]['nombre'],
                        'descripcion_corta'=>$productos[$i]['descripcion_corta'],
                        'precio_unitario'=>$productos[$i]['moneda'] == "USD" ? number_format((($productos[$i]['precio']*$productos[$i]['tipoCambio'])*1.16),2,'.',''):number_format(($productos[$i]['precio']*1.16),2,'.',''),
                        'sku'=>ltrim($productos[$i]['numParte']),
                        'ean'=>$productos[$i]['ean'],
                        'upc'=>$productos[$i]['upc'],
                        'imagen'=>$productos[$i]['imagen'],
                        'existencias'=>$existencia_producto,
                        'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
                    ]
                );
                if(!empty($productos[$i]['promociones'])){
                    // dd($productos[$i]['promociones'][0]['vigencia']['inicio']);
                    // date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio']));
                    // date('Y-m-d\TH:i:s', $productos[$i]['promociones'][0]['vigencia']['inicio']);
                    if($productos[$i]['promociones'][0]['tipo']!="porcentaje"){
                        $promocion = Promocion::updateOrCreate(
                            ['clave_ct'=>$productos[$i]['clave']],
                            ['descuento'=>100-($productos[$i]['promociones'][0]['promocion']*100)/$productos[$i]['precio'],
                            'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
                            'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
                        );
                    }else{
                        $promocion = Promocion::updateOrCreate(
                            ['clave_ct'=>$productos[$i]['clave']],
                            ['descuento'=>$productos[$i]['promociones'][0]['promocion'],
                            'fecha_inicio'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['inicio'])),
                            'fecha_fin'=>date('Y-m-d', strtotime($productos[$i]['promociones'][0]['vigencia']['fin']))]
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
                $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
                for($j=0;$j<sizeof($palabras_clave);$j++){
                    $producto = Palabras::updateOrCreate(
                        ['clave_ct'=>$productos[$i]['clave'],
                        'palabra'=>$palabras_clave[$j]]
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

    public function existencias ($productos){
        set_time_limit(0);
        //dd($productos);
            $existencia_producto=0;
            if(!empty($productos['existencia']['DFA'])){
            $existencia_producto += $productos['existencia']['DFA'];
            }
            if(!empty($productos['existencia']['D2A'])){
            $existencia_producto += $productos['existencia']['D2A'];
            }
            if(!empty($productos['existencia']['CAM'])){
            $existencia_producto += $productos['existencia']['CAM'];
            }
            if(!empty($productos['existencia']['GDL'])){
            $existencia_producto += $productos['existencia']['GDL'];
            // dd($productos['existencia']['GDL'].$productos['clave']);
            }
            if(!empty($productos['existencia']['ZAC'])){
            $existencia_producto += $productos['existencia']['ZAC'];
            }
            if(!empty($productos['existencia']['ACA'])){
            $existencia_producto += $productos['existencia']['ACA'];
            }
            if(!empty($productos['existencia']['QRO'])){
            $existencia_producto += $productos['existencia']['QRO'];
            }
            if(!empty($productos['existencia']['COL'])){
            $existencia_producto += $productos['existencia']['COL'];
            }
            if(!empty($productos['existencia']['HMO'])){
            $existencia_producto += $productos['existencia']['HMO'];
            }
            if(!empty($productos['existencia']['LMO'])){
            $existencia_producto += $productos['existencia']['LMO'];
            }
            if(!empty($productos['existencia']['CLN'])){
            $existencia_producto += $productos['existencia']['CLN'];
            }
            if(!empty($productos['existencia']['CHI'])){
            $existencia_producto += $productos['existencia']['CHI'];
            }
            if(!empty($productos['existencia']['MOR'])){
            $existencia_producto += $productos['existencia']['MOR'];
            }
            if(!empty($productos['existencia']['VER'])){
            $existencia_producto += $productos['existencia']['VER'];
            }
            if(!empty($productos['existencia']['CTZ'])){
            $existencia_producto += $productos['existencia']['CTZ'];
            }
            if(!empty($productos['existencia']['TAM'])){
            $existencia_producto += $productos['existencia']['TAM'];
            }
            if(!empty($productos['existencia']['PUE'])){
            $existencia_producto += $productos['existencia']['PUE'];
            }
            if(!empty($productos['existencia']['VHA'])){
            $existencia_producto += $productos['existencia']['VHA'];
            }
            if(!empty($productos['existencia']['TUX'])){
            $existencia_producto += $productos['existencia']['TUX'];
            }
            if(!empty($productos['existencia']['MTY'])){
            $existencia_producto += $productos['existencia']['MTY'];
            }
            if(!empty($productos['existencia']['MID'])){
            $existencia_producto += $productos['existencia']['MID'];
            }
            if(!empty($productos['existencia']['MAZ'])){
            $existencia_producto += $productos['existencia']['MAZ'];
            }
            if(!empty($productos['existencia']['CUE'])){
            $existencia_producto += $productos['existencia']['CUE'];
            }
            if(!empty($productos['existencia']['CUN'])){
            $existencia_producto += $productos['existencia']['CUN'];
            }
            if(!empty($productos['existencia']['DFP'])){
            $existencia_producto += $productos['existencia']['DFP'];
            }
            if(!empty($productos['existencia']['ACX'])){
            $existencia_producto += $productos['existencia']['ACX'];
            }
            if(!empty($productos['existencia']['CEL'])){
            $existencia_producto += $productos['existencia']['CEL'];
            }
            if(!empty($productos['existencia']['OBR'])){
            $existencia_producto += $productos['existencia']['OBR'];
            }
            if(!empty($productos['existencia']['DGO'])){
            $existencia_producto += $productos['existencia']['DGO'];
            }
            if(!empty($productos['existencia']['TRN'])){
            $existencia_producto += $productos['existencia']['TRN'];
            }
            if(!empty($productos['existencia']['AGS'])){
            $existencia_producto += $productos['existencia']['AGS'];
            }
            if(!empty($productos['existencia']['SLP'])){
            $existencia_producto += $productos['existencia']['SLP'];
            }
            if(!empty($productos['existencia']['XLP'])){
            $existencia_producto += $productos['existencia']['XLP'];
            }
            if(!empty($productos['existencia']['DFT'])){
            $existencia_producto += $productos['existencia']['DFT'];
            }
            if(!empty($productos['existencia']['CDV'])){
            $existencia_producto += $productos['existencia']['CDV'];
            }
            if(!empty($productos['existencia']['SLT'])){
            $existencia_producto += $productos['existencia']['SLT'];
            }
            if(!empty($productos['existencia']['TPC'])){
            $existencia_producto += $productos['existencia']['TPC'];
            }
            if(!empty($productos['existencia']['TOL'])){
            $existencia_producto += $productos['existencia']['TOL'];
            }
            if(!empty($productos['existencia']['PAC'])){
            $existencia_producto += $productos['existencia']['PAC'];
            }
            if(!empty($productos['existencia']['IRA'])){
            $existencia_producto += $productos['existencia']['IRA'];
            }
            if(!empty($productos['existencia']['OAX'])){
            $existencia_producto += $productos['existencia']['OAX'];
            }
            if(!empty($productos['existencia']['DFC'])){
            $existencia_producto += $productos['existencia']['DFC'];
            }
            if(!empty($productos['existencia']['TXL'])){
            $existencia_producto += $productos['existencia']['TXL'];
            }
            if(!empty($productos['existencia']['URP'])){
            $existencia_producto += $productos['existencia']['URP'];
            }
            //dd($existencia_producto);
            return $existencia_producto;
    }
}