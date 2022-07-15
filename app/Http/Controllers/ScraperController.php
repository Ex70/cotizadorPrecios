<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Support\Facades\DB;

class ScraperController extends Controller{
    public function index(){
        $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtroscyberpuerta',compact('data'));
    }

    public function cotizar(Request $request){
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $skus = DB::table('productos')->select('id','sku')->where('id','>=','4999')->where('id','<=','5001')->orderBy('id', 'ASC')->get()->toArray();
        $client = new Client();
        $precios = [];
        for($i=0;$i<sizeof($skus);$i++){
            $sku = $skus[$i]->sku;
            if($sku==""){
                $sku="NOEXISTE";
            }
            // try{
                $website = $client->request('GET', 'https://www.cyberpuerta.mx/index.php?cl=search&searchparam=HD680');
                // $precios[$i] = $website->count() ? $precios[$i] = 1 : $precios[$i] = 0;
                $result = $website->filter('#content > .price');
                // $result = $website->filter('.emproduct_right_price_left > .price')->first()->text();
                $precios[$i] = $result->count() ? $website->filter('#content > .price')->first()->text() : $precios[$i] = 0;
                
            // }catch(Exception $e){
                // echo "NO";
            // }

            // $url = "https://mipc.com.mx/search/ajax/suggest/?q=".$sku;
            // $res = $client->request('GET', $url);
            // $result = $res->getBody();
            // $data = json_decode($result, true);
            // $results = $this->searchJson( $data , 'price' );
            // dd($results);
            // print_r( $results );
            echo $sku." - ".$precios[$i];
            // $precios[$i] = $data;
        }
        $data['precios'] = $precios;
        // dd($data['precios']);
        
        $data['categoria'] = $request->get('filtro1');
        $data['subcategoria'] = $request->get('filtro2');
        $data['productos'] = DB::table('productos')->select('id','descripcion')->where('estatus','Activo')->where('categoria',$test)->where('subcategoria',$test2)->orderBy('id', 'ASC')->get()->toArray();
        $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtrosmipc',compact('data'));
    }

    public function index2(){
        // $client = new Client();    
        // $website = $client->request('GET', 'https://www.businesslist.com.ng/category/interior-design/city:lagos');
        // return $website->html();
        $client = new Client();
        $website = $client->request('GET', 'https://www.cyberpuerta.mx/index.php?cl=search&searchparam=hd680');
        $companias = $website->filter('.emproduct_right_price_left > .price')->first()->text();
        // dd($companias);
        echo $companias;
        $companies = $website->filter('.emproduct_right_price_left')->each(function ($node) {
            $node->children('.price')->each(function ($child) {
                return $prueba = $child->text();
            });
            // dump($node->text());
            // dd($node->children()->first()->text());
            // return $node->children()->first()->text();
            // return [
            //     'first_item' => $node->children()->eq(0)->text(),
            //     'first_item_again' => $node->children()->first()->text(),
            //     'second_item' => $node->children()->eq(1)->text(),
            //     'last_item' => $node->children()->last()->text(),
            // ];
        
        });
        // print_r($companies);
        // dd($companies);
        // $companies = $website->filter('.company')->each(function ($node) {
        //     return [
        //         'first_item' => $node->children()->eq(0)->text(),
        //         'first_item_again' => $node->children()->first()->text(),
        //         'second_item' => $node->children()->eq(1)->text(),
        //         'last_item' => $node->children()->last()->text(),
        //     ];
        // });
    }
}
