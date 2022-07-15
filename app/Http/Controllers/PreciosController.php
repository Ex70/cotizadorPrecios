<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class PreciosController extends Controller
{
    public function index(){
        $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtros',compact('data'));
    }

    public function getCategorias($id = null){
        $data = DB::table('productos')->distinct()->where('categoria',$id)->get(['subcategoria']);
        // $data = sopList::where('doc_no',$id)->first();
        return response()->json($data);
    }

    public function cotizar(Request $request){
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        // $skus = DB::table('productos')->select('id','sku')->where('estatus','Activo')->where('categoria',$test)->where('subcategoria',$test2)->orderBy('id', 'ASC')->get()->toArray();
        $skus = DB::table('productos')->select('id','sku')->where('id','>=','39001')->where('id','<=','40000')->orderBy('id', 'ASC')->get()->toArray();
        // dd(sizeof($skus));
        // $skus = DB::table('productos')->select('id','sku')->where('id','>=','0')->where('id','<=','100')->where('estatus','Activo')->orderBy('id', 'ASC')->get()->toArray();
        // $skus = DB::table('productos')->select('id','sku')->where('estatus','Activo')->orderBy('id', 'DESC')->get()->toArray();
        $client = new Client();
        $precios = [];
        for($i=0;$i<sizeof($skus);$i++){
            $sku = $skus[$i]->sku;
            if($sku==""){
                $sku="NOEXISTE";
                // dd($skus[$i]->id);
                // print_r("NO EXISTE");
            }
            // $sku = "AD3S1600W4G11-S";
            $url = "https://www.cyberpuerta.mx/widget.php?cl=cpmobile_ajax&fnc=getSearchSuggest&q=".$sku."&userEmail=&skipSession=1";
            $res = $client->request('GET', $url);
            $result = $res->getBody();
            $data = json_decode($result, true);
            if(empty($data['results']['articleIds'])){
                $precios[$i]= 0;
                $data2 = "";
                // dd("0o0o0o");
            }else{
                $id = $data['results']['articleIds'][0];
                $url = "https://www.cyberpuerta.mx/widget.php?cl=cpmobile_ajax&fnc=getArticles&skipSession=1&ids%5B%5D=".$id;
                $res = $client->request('GET', $url);
                $result = $res->getBody();
                $data2 = json_decode($result, true);
                // dd($url);
            }
            // dd($data['results']['categories'][0]['id']);
            // if($data)
            if(!empty($data2['articles'])){
                // dd($data2['articles'][0]['price']);
                $precios[$i]= $data2['articles'][0]['price'];
            }else{
                $precios[$i]= 0;
            }
            // $result->count() ? $website->filter('#content > .price')->first()->text() : $precios[$i] = 0
            // dd($data['precios']);
            // print_r($sku);
            // print_r(" - ".$data['price'][0]);
            // $precios = array_merge($precios, $data);
            // print_r("\n");
        }
        // dd($precios);
        $data['precios'] = $precios;
        // dd($data['precios']);
        $data['categoria'] = $request->get('filtro1');
        $data['subcategoria'] = $request->get('filtro2');
        $data['productos'] = DB::table('productos')->select('id','descripcion')->where('estatus','Activo')->where('categoria',$test)->where('subcategoria',$test2)->orderBy('id', 'ASC')->get()->toArray();
        $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtros',compact('data'));
    }
}