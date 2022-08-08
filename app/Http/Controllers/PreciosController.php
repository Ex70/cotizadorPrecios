<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\PreciosAbasteoController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Models\Producto;

class PreciosController extends Controller{
    public function index(){
        $data['categorias'] = Categoria::distinct('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->get();
        // $data['categoriasPrueba'] = Subcategoria::find(1);
        // dd($data['categorias'][0]->nombre);
        // $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        // $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtros',compact('data'));
    }

    public function getCategorias($id = null){
        $data = Subcategoria::distinct('nombre')->where('categoria_id',$id)->get();
        return response()->json($data);
    }

    public function cotizar(Request $request){
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $productos = Producto::where('categoria_id',$test)->where('subcategoria_id',$test2)->get();
        // dd($productos[0]['sku']);
        // $productos = Producto::where('categoria_id',$test)->where('subcategoria_id',$test2)->get()->toArray();
        // print_r($skus);
        // dd($skus);

        $preciosAbasteo = new PreciosAbasteoController;

        $abasteo = $preciosAbasteo->cotizar($productos);
        // dd($abasteo);

        $client = new Client();
        $precios = [];
        // dd(sizeof($skus));
        // for($i=0;$i<sizeof($skus);$i++){
        //     $sku = $skus[$i]->sku;
        //     if($sku==""){
        //         $sku="NOEXISTE";
        //     }
        //     $url = "https://www.cyberpuerta.mx/widget.php?cl=cpmobile_ajax&fnc=getSearchSuggest&q=".$sku."&userEmail=&skipSession=1";
        //     $res = $client->request('GET', $url);
        //     $result = $res->getBody();
        //     $data = json_decode($result, true);
        //     if(empty($data['results']['articleIds'])){
        //         $precios[$i]= 0;
        //         $data2 = "";
        //     }else{
        //         $id = $data['results']['articleIds'][0];
        //         $url = "https://www.cyberpuerta.mx/widget.php?cl=cpmobile_ajax&fnc=getArticles&skipSession=1&ids%5B%5D=".$id;
        //         $res = $client->request('GET', $url);
        //         $result = $res->getBody();
        //         $data2 = json_decode($result, true);
        //     }
        //     if(!empty($data2['articles'])){
        //         $precios[$i]= $data2['articles'][0]['price'];
        //     }else{
        //         $precios[$i]= 0;
        //     }
        // }
        $data['productos'] = $productos;
        $data['abasteo'] = $abasteo;
        // dd($data['abasteo']);
        $data['categoria'] = $request->get('filtro1');
        $data['subcategoria'] = $request->get('filtro2');
        // $data['productos'] = DB::table('productos')->select('id','descripcion')->where('estatus','Activo')->where('categoria',$test)->where('subcategoria',$test2)->orderBy('id', 'ASC')->get()->toArray();
        $data['categorias'] = Categoria::distinct('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->get();
        // $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        // $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtros',compact('data'));
    }
}