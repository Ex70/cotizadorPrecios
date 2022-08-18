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
use App\Models\Subcategoria;
use App\Models\Producto;

class PreciosController extends Controller{
    public function index(){
        $data['categorias'] = Categoria::distinct('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->get();
        $data['productos'] = '';
        return view('filtros',compact('data'));
    }

    public function getCategorias($id = null){
        $data = Subcategoria::distinct('nombre')->where('categoria_id',$id)->get();
        return response()->json($data);
    }

    public function getMarcas($id = null,$id2 = null){
        // dd($id."-".$id2);
        // $data = Producto::distinct('marca_id')->where('categoria_id',$id)->where('subcategoria_id',$id2)->get();
        // $data = Marca::distinct('nombre')->join('productos', 'marcas.id', '=', 'productos.marca_id')->where('marcas.id','<',100)->get();
        // ->join('productos', 'marcas.id', '=', 'productos.marca_id')->where('categoria_id',$id)->get();
        $sql = "SELECT * FROM users WHERE estado=? AND edad<?";
        $sql = "select id,nombre from marcas where id IN(select DISTINCT marca_id from productos where categoria_id = ? and subcategoria_id = ?)";
        $data = DB::select($sql,array($id,$id2));
        // $data = DB::table('marcas')
        // dd($data);
        return response()->json($data);
    }

    public function cotizar(Request $request){
        $preciosAbasteo = new PreciosAbasteoController;
        $preciosCyberpuerta = new CyberPuertaController;
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
            $data['cyberpuerta'] = $preciosCyberpuerta->cotizar($data['productos']);
            $data['mipc'] = $preciosMiPC->cotizar($data['productos']);
            $data['zegucom'] = $preciosZegucom->cotizar($data['productos']);
            $data['categoria'] = $request->get('filtro1');
            $data['subcategoria'] = $request->get('filtro2');
        }
        $data['categorias'] = Categoria::distinct('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->get();
        // dd(sizeof($data['productos']));
        return view('filtros',compact('data'));
    }
}