<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\PreciosAbasteoController;
use App\Http\Controllers\CyberPuertaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Categoria;
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
        $productos = json_decode(Storage::get('public/products.json'),true);
        set_time_limit(0);
        for($i=0;$i<sizeof($productos);$i++){
            if($productos[$i]['idCategoria']!=0){
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
                    // dd($productos[$i]['clave']);
                }
                $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
                for($j=0;$j<sizeof($palabras_clave);$j++){
                    $producto = Palabras::updateOrCreate(
                        ['clave_ct'=>$productos[$i]['clave'],
                        'palabra'=>$palabras_clave[$j]]
                    );
                }
            }
        }
        dd($productos);
    }

    public function lecturaLocal (){
        $products = new ProductosController();
        $existencia_producto=0;
         $products->limpieza();
        $productos = json_decode(file_get_contents(storage_path() . "/app/public/productos.json"), true);
        // dd(storage_path() . "/app/public/productos.json");
        // dd($productos);
        set_time_limit(0);
        for($i=0;$i<sizeof($productos);$i++){
            if($productos[$i]['idCategoria']!=0){
                // PRUEBA EXISTENCIAS
                    // if(!empty($productos[$i]['existencia']['ACA'])){
                    //     $existencia_producto += $productos[$i]['existencia']['ACA'];
                    // }
                    // if(!empty($productos[$i]['existencia']['ACX'])){
                    //     $existencia_producto += $productos[$i]['existencia']['ACX'];
                    // }
                    // if(!empty($productos[$i]['existencia']['AGS'])){
                    //     $existencia_producto += $productos[$i]['existencia']['AGS'];
                    // }
                    // if(!empty($productos[$i]['existencia']['CAM'])){
                    //     $existencia_producto += $productos[$i]['existencia']['CAM'];
                    // }
                    // if(!empty($productos[$i]['existencia']['CDV'])){
                    //     $existencia_producto += $productos[$i]['existencia']['CDV'];
                    // }
                    // if(!empty($productos[$i]['existencia']['CEL'])){
                    //     $existencia_producto += $productos[$i]['existencia']['CEL'];
                    // }
                    // if(!empty($productos[$i]['existencia']['CHI'])){
                    //     $existencia_producto += $productos[$i]['existencia']['CHI'];
                    // }
                    // if(!empty($productos[$i]['existencia']['CLN'])){
                    //     $existencia_producto += $productos[$i]['existencia']['CLN'];
                    // }
                    // if(!empty($productos[$i]['existencia']['COL'])){
                    //     $existencia_producto += $productos[$i]['existencia']['COL'];
                    // }
                    // if(!empty($productos[$i]['existencia']['CTZ'])){
                    //     $existencia_producto += $productos[$i]['existencia']['CTZ'];
                    // }
                    // if(!empty($productos[$i]['existencia']['CUE'])){
                    //     $existencia_producto += $productos[$i]['existencia']['CUE'];
                    // }
                    // if(!empty($productos[$i]['existencia']['CUN'])){
                    //     $existencia_producto += $productos[$i]['existencia']['CUN'];
                    // }
                    // if(!empty($productos[$i]['existencia']['D2A'])){
                    //     $existencia_producto += $productos[$i]['existencia']['D2A'];
                    // }
                    // if(!empty($productos[$i]['existencia']['DGO'])){
                    //     $existencia_producto += $productos[$i]['existencia']['DGO'];
                    // }
                    // if(!empty($productos[$i]['existencia']['DFA'])){
                    //     $existencia_producto += $productos[$i]['existencia']['DFA'];
                    // }
                    // if(!empty($productos[$i]['existencia']['DFC'])){
                    //     $existencia_producto += $productos[$i]['existencia']['DFC'];
                    // }
                    // if(!empty($productos[$i]['existencia']['DFP'])){
                    //     $existencia_producto += $productos[$i]['existencia']['DFP'];
                    // }
                    // if(!empty($productos[$i]['existencia']['DFT'])){
                    //     $existencia_producto += $productos[$i]['existencia']['DFT'];
                    // }
                    // if(!empty($productos[$i]['existencia']['GDL'])){
                    //     $existencia_producto += $productos[$i]['existencia']['GDL'];
                    // }
                    // if(!empty($productos[$i]['existencia']['HMO'])){
                    //     $existencia_producto += $productos[$i]['existencia']['HMO'];
                    // }
                    // if(!empty($productos[$i]['existencia']['IRA'])){
                    //     $existencia_producto += $productos[$i]['existencia']['IRA'];
                    // }
                    // if(!empty($productos[$i]['existencia']['LMO'])){
                    //     $existencia_producto += $productos[$i]['existencia']['LMO'];
                    // }
                    // if(!empty($productos[$i]['existencia']['MAZ'])){
                    //     $existencia_producto += $productos[$i]['existencia']['MAZ'];
                    // }
                    // if(!empty($productos[$i]['existencia']['MID'])){
                    //     $existencia_producto += $productos[$i]['existencia']['MID'];
                    // }
                    // if(!empty($productos[$i]['existencia']['MOR'])){
                    //     $existencia_producto += $productos[$i]['existencia']['MOR'];
                    // }
                    // if(!empty($productos[$i]['existencia']['MTY'])){
                    //     $existencia_producto += $productos[$i]['existencia']['MTY'];
                    // }
                    // if(!empty($productos[$i]['existencia']['OAX'])){
                    //     $existencia_producto += $productos[$i]['existencia']['OAX'];
                    // }
                    // if(!empty($productos[$i]['existencia']['OBR'])){
                    //     $existencia_producto += $productos[$i]['existencia']['OBR'];
                    // }
                    // if(!empty($productos[$i]['existencia']['PAC'])){
                    //     $existencia_producto += $productos[$i]['existencia']['PAC'];
                    // }
                    // if(!empty($productos[$i]['existencia']['PUE'])){
                    //     $existencia_producto += $productos[$i]['existencia']['PUE'];
                    // }
                    // if(!empty($productos[$i]['existencia']['QRO'])){
                    //     $existencia_producto += $productos[$i]['existencia']['QRO'];
                    // }
                    // if(!empty($productos[$i]['existencia']['SLP'])){
                    //     $existencia_producto += $productos[$i]['existencia']['SLP'];
                    // }
                    // if(!empty($productos[$i]['existencia']['SLT'])){
                    //     $existencia_producto += $productos[$i]['existencia']['SLT'];
                    // }
                    // if(!empty($productos[$i]['existencia']['TAM'])){
                    //     $existencia_producto += $productos[$i]['existencia']['TAM'];
                    // }
                    // if(!empty($productos[$i]['existencia']['TOL'])){
                    //     $existencia_producto += $productos[$i]['existencia']['TOL'];
                    // }
                    // if(!empty($productos[$i]['existencia']['TPC'])){
                    //     $existencia_producto += $productos[$i]['existencia']['TPC'];
                    // }
                    // if(!empty($productos[$i]['existencia']['TRN'])){
                    //     $existencia_producto += $productos[$i]['existencia']['TRN'];
                    // }
                    // if(!empty($productos[$i]['existencia']['TUX'])){
                    //     $existencia_producto += $productos[$i]['existencia']['TUX'];
                    // }
                    // if(!empty($productos[$i]['existencia']['TXL'])){
                    //     $existencia_producto += $productos[$i]['existencia']['TXL'];
                    // }
                    // if(!empty($productos[$i]['existencia']['VER'])){
                    //     $existencia_producto += $productos[$i]['existencia']['VER'];
                    // }
                    // if(!empty($productos[$i]['existencia']['VHA'])){
                    //     $existencia_producto += $productos[$i]['existencia']['VHA'];
                    // }
                    // if(!empty($productos[$i]['existencia']['XLP'])){
                    //     $existencia_producto += $productos[$i]['existencia']['XLP'];
                    // }
                    // if(!empty($productos[$i]['existencia']['ZAC'])){
                    //     $existencia_producto += $productos[$i]['existencia']['ZAC'];
                    // }
                // dd($existencia_producto);
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
                        //'existencias'=>$existencia_producto,
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
                    // dd($productos[$i]['clave']);
                }
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
        dd($productos);
    }
}