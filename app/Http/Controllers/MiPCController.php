<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class MiPCController extends Controller{
    public function index(){
        $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtrosmipc',compact('data'));
    }

    public function cotizar(Request $request){
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $skus = DB::table('productos')->select('id','sku')->where('id','>=','9001')->where('id','<=','11000')->orderBy('id', 'ASC')->get()->toArray();
        $client = new Client();
        $precios = [];
        for($i=0;$i<sizeof($skus);$i++){
            $sku = $skus[$i]->sku;
            if($sku==""){
                $sku="NOEXISTE";
            }
            $url = "https://mipc.com.mx/search/ajax/suggest/?q=".$sku;
            $res = $client->request('GET', $url);
            $result = $res->getBody();
            $data = json_decode($result, true);
            // $results = $this->searchJson( $data , 'price' );
            // dd($results);
            // print_r( $results );
            echo $sku." - ";


            // $results = array_filter($data['price'], function($data) {
            //     return $data['price'];
            // });
            // dd($results);

            $precios[$i] = $data;

            // print_r(count($data));
            if(count($data)>0){
                if(count($data)>=4){
                    // echo $data->price;
                    // $clave = array_search('price', $data);
                    // echo " -- ";
                    // echo $clave;
                    // echo ".".$sku;
                    // print_r($data);
                    $precios[$i] = $data[3]['price'];
                }else{
                    if(count($data)==3){
                        $precios[$i] = $data[2]['price'];
                    }else{
                        if(count($data)==2){
                            $precios[$i] = $data[1]['price'];
                        }else{
                            // if(in_array("price",$data[0])){
                            //     dd ("Tiene precio");
                            // }else{
                            //     echo ("No tiene precio");
                            // }
                            if(in_array("price",$data[0])){
                                if($data[0]['price']){
                                    $precios[$i] = $data[0]['price'];
                                }
                            }else{
                                $precios[$i] = 0;
                            }
                        }
                    }
                }    
            }else{
                $precios[$i] = 0;
            }
            // $precios[$i] = $data[1]['price'];
            // dd($data[1]['price']);
            // dd($precios);
            // echo $precio;
            // print_r($sku);
            // print_r(" - ".$data['price'][0]);
            // $precios[$i]= $data['price'][0];
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
        return view('filtrosmipc',compact('data'));
    }

    function searchJson( $obj, $value ) {
        foreach( $obj as $key => $item ) {
            if( !is_nan( intval( $key ) ) && is_array( $item ) ){
                if( in_array( $value, $item ) ) return $item;
            } else {
                foreach( $item as $child ) {
                    if(isset($child) && $child == $value) {
                        return $child;
                    }
                }
            }
        }
        return null;
    }
}
