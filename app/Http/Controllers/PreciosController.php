<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\PreciosAbasteoController;
use App\Http\Controllers\CyberPuertaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Producto;

class PreciosController extends Controller{
    public function index(){
        $data['categorias'] = Categoria::distinct('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->get();
        return view('filtros',compact('data'));
    }

    public function getCategorias($id = null){
        $data = Subcategoria::distinct('nombre')->where('categoria_id',$id)->get();
        return response()->json($data);
    }

    public function cotizar(Request $request){
        $preciosAbasteo = new PreciosAbasteoController;
        $preciosCyberpuerta = new CyberPuertaController;
        $preciosMiPC = new MiPCController;
        $preciosZegucom = new ZegucomController;
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $data['productos'] = Producto::where('categoria_id',$test)->where('subcategoria_id',$test2)->where('estatus','Activo')->get();
        $data['abasteo'] = $preciosAbasteo->cotizar($data['productos']);
        $data['cyberpuerta'] = $preciosCyberpuerta->cotizar($data['productos']);
        $data['mipc'] = $preciosMiPC->cotizar($data['productos']);
        $data['zegucom'] = $preciosZegucom->cotizar($data['productos']);
        $data['categoria'] = $request->get('filtro1');
        $data['subcategoria'] = $request->get('filtro2');
        $data['categorias'] = Categoria::distinct('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->get();
        return view('filtros',compact('data'));
    }
}