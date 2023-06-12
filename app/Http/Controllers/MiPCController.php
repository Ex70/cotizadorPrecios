<?php

namespace App\Http\Controllers;

use App\Models\Mipc;
use Goutte\Client as GoutteClient;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class MiPCController extends Controller{
    public function index(){
        $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtrosmipc',compact('data'));
    }

    public function cotizar($productos){
        set_time_limit(0);
        $remove = array(" ","  ","   ","    ", "(", ")", "$", "*", "/",",");
        // $client = new Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
        $client = new GoutteClient();
        $precios = [];
        for($i=0;$i<sizeof($productos);$i++){
            $sku = $productos [$i]->sku;
            $clave_ct = $productos[$i]->clave_ct;
            if($sku==""){
                $sku="NOEXISTE";
            }
            $url = "https://mipc.com.mx/search/ajax/suggest/?q=".$sku;
            // dd($sku);
            $website = $client->request('GET', 'https://mipc.com.mx/buscar/?q='.$sku.'');
            // $result = $website->filter('.price-cash')->first()->text();
            // dd($result);
            // $website = $client->request('GET', 'https://www.zegucom.com.mx/productos/search?search=CS400C-5BBC');
            // $result = $website->filter('.search-price-now > .search-price-now-value ');
            $result = $website->filter('.price-cash');
            // dd($result);
            // dd($result->count());
            // dd(str_replace($remove, "", $website->filter('.text-darken-4')->first()->text()));
            // $result = $website->filter('.price-text > .result-price-search');
            // $precios[$i] = $result->count() ? str_replace($remove, "", $website->filter('.price-text > .result-price-search')->first()->text()) : $precios[$i] = 0;
            $precios[$i] = $result->count()>0 ? str_replace($remove, "", $website->filter('.price-cash')->first()->text()) : $precios[$i] = 0;
            // dd($precios);
            $productoMiPC = Mipc::updateOrCreate(
                ['sku'=>$sku, 'clave_ct'=>$clave_ct],
                ['precio_unitario'=>$precios[$i]]
            );
            // dd($productoMiPC);
        // }
        // return $precios;


            // $res = $client->request('GET', $url);
            // $result = $res->getBody();
            // $data = json_decode($result, true);
            // $precios[$i] = $data;
            // if(count($data)>=2){
            //     dd($data[1]['price']);
            // }
            // if(count($data)>0){
            //     if(count($data)>=5){
            //         if(strpos(str_replace($remove, "", strip_tags($data[4]['price'])), "PrecioEspecial") !== false){
            //             $string = str_replace($remove, "", strip_tags($data[4]['price']));
            //             $start = 'PrecioEspecial';
            //             $end = 'PrecioRegular';
            //             $startpos = strpos($string, $start) + strlen($start);
            //             if (strpos($string, $start) !== false) {
            //                 $endpos = strpos($string, $end, $startpos);
            //                 if (strpos($string, $end, $startpos) !== false) {
            //                     $precios[$i] = substr($string, $startpos, $endpos - $startpos);
            //                 }
            //             }
            //         } else{
            //             $precios[$i] = str_replace($remove, "", strip_tags($data[4]['price']));
            //         }
            //     }else{
            //         if(count($data)>=4){
            //             if(strpos(str_replace($remove, "", strip_tags($data[3]['price'])), "PrecioEspecial") !== false){
            //                 $string = str_replace($remove, "", strip_tags($data[3]['price']));
            //                 $start = 'PrecioEspecial';
            //                 $end = 'PrecioRegular';
            //                 $startpos = strpos($string, $start) + strlen($start);
            //                 if (strpos($string, $start) !== false) {
            //                     $endpos = strpos($string, $end, $startpos);
            //                     if (strpos($string, $end, $startpos) !== false) {
            //                         $precios[$i] = substr($string, $startpos, $endpos - $startpos);
            //                     }
            //                 }
            //             } else{
            //                 $precios[$i] = str_replace($remove, "", strip_tags($data[3]['price']));
            //             }
            //         }else{
            //             if(count($data)==3){
            //                 if(!empty($data[2]['price'] )){
            //                     if(strpos(str_replace($remove, "", strip_tags($data[2]['price'])), "PrecioEspecial") !== false){
            //                         $string = str_replace($remove, "", strip_tags($data[2]['price']));
            //                         $start = 'PrecioEspecial';
            //                         $end = 'PrecioRegular';
            //                         $startpos = strpos($string, $start) + strlen($start);
            //                         if (strpos($string, $start) !== false) {
            //                             $endpos = strpos($string, $end, $startpos);
            //                             if (strpos($string, $end, $startpos) !== false) {
            //                                 $precios[$i] = substr($string, $startpos, $endpos - $startpos);
            //                             }
            //                         }
            //                     } else{
            //                         $precios[$i] = str_replace($remove, "", strip_tags($data[2]['price']));
            //                     }
            //                 }else{
            //                     if(strpos(str_replace($remove, "", strip_tags($data[1]['price'])), "PrecioEspecial") !== false){
            //                         $string = str_replace($remove, "", strip_tags($data[1]['price']));
            //                         $start = 'PrecioEspecial';
            //                         $end = 'PrecioRegular';
            //                         $startpos = strpos($string, $start) + strlen($start);
            //                         if (strpos($string, $start) !== false) {
            //                             $endpos = strpos($string, $end, $startpos);
            //                             if (strpos($string, $end, $startpos) !== false) {
            //                                 $precios[$i] = substr($string, $startpos, $endpos - $startpos);
            //                             }
            //                         }
            //                     } else{
            //                         $precios[$i] = str_replace($remove, "", strip_tags($data[1]['price']));
            //                     }
                                
            //                 }
                            
            //             }else{
            //                 if(count($data)==2){
            //                     if(strpos(str_replace($remove, "", strip_tags($data[1]['price'])), "PrecioEspecial") !== false){
            //                         $string = str_replace($remove, "", strip_tags($data[1]['price']));
            //                         $start = 'PrecioEspecial';
            //                         $end = 'PrecioRegular';
            //                         $startpos = strpos($string, $start) + strlen($start);
            //                         if (strpos($string, $start) !== false) {
            //                             $endpos = strpos($string, $end, $startpos);
            //                             if (strpos($string, $end, $startpos) !== false) {
            //                                 $precios[$i] = substr($string, $startpos, $endpos - $startpos);
            //                             }
            //                         }
            //                     } else{
            //                         $precios[$i] = str_replace($remove, "", strip_tags($data[1]['price']));
            //                     }
            //                 }else{
            //                     if(in_array("price",$data[0])){
            //                         if($data[0]['price']){
            //                             if(strpos(str_replace($remove, "", strip_tags($data[0]['price'])), "PrecioEspecial") !== false){
            //                                 $string = str_replace($remove, "", strip_tags($data[0]['price']));
            //                                 $start = 'PrecioEspecial';
            //                                 $end = 'PrecioRegular';
            //                                 $startpos = strpos($string, $start) + strlen($start);
            //                                 if (strpos($string, $start) !== false) {
            //                                     $endpos = strpos($string, $end, $startpos);
            //                                     if (strpos($string, $end, $startpos) !== false) {
            //                                         $precios[$i] = substr($string, $startpos, $endpos - $startpos);
            //                                     }
            //                                 }
            //                             } else{
            //                                 $precios[$i] = str_replace($remove, "", strip_tags($data[0]['price']));
            //                             }
            //                         }
            //                     }else{
            //                         $precios[$i] = 0;
            //                     }
            //                 }
            //             }
            //         }
            //     }
            // }else{
            //     $precios[$i] = 0;
            // }
            // $productoMiPC = Mipc::updateOrCreate(
            //     ['sku'=>$sku, 'clave_ct'=>$clave_ct],
            //     ['precio_unitario'=>$precios[$i]]
            // );
        }
        return $precios;
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
