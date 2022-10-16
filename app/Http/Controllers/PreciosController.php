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
            $existencias = new CTConnect;
            $data['existencias'] = $existencias->existencias($data['productos']);
        }
        $data['categorias'] = Categoria::distinct('nombre')->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        return view('filtros',compact('data'));
    }

    public function lectura(){
        $products = new ProductosController();
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
                        'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
                    ]
                );
            }
        }
        dd($productos);
    }

    public function lecturaLocal (){
        $products = new ProductosController();
        $products->limpieza();
        $productos = json_decode(file_get_contents(storage_path() . "/app/public/productos.json"), true);
        // dd(storage_path() . "/app/public/productos.json");
        // dd($productos);
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
                        'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
                    ]
                );
                $palabras_clave = explode(",",$productos[$i]['descripcion_corta']);
                for($j=0;$j<sizeof($palabras_clave);$j++){
                    $producto = Palabras::updateOrCreate(
                        ['clave_ct'=>$productos[$i]['clave'],
                        'palabra'=>$palabras_clave[$j]]
                    );
                }
            }
        }
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