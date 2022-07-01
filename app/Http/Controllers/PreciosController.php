<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class PreciosController extends Controller
{
    public function index(){
        // $client = new Client([(['base_uri' => 'https://reqres.in/'])]);
        // $response = $client->request('GET', 'https://www.abasteo.mx/api/v0.1/catalog/filter?type=search&id=VENTO120-BKCWW');
        // dd($response);
        // $client = new Client();
        // $url = "https://reqres.in/api/users=page=1";
        // $myBody['title'] = "This is a title";
        // $request = $client->get($url, ['form_params' => [
        //     'name' => 'Dan',
        //     'job' => 'Full Stack Dev']]);
        // $response = $request();
        // $data = json_decode($response->getBody());
        // dd($data['price']);
        
        
        
        // $data = $response->getBody();
        // print($data["price"]);
        // $skus = ['140065','701662','700320','700955','NORTH712-BKL','034600','CLP0608','CW-9060040-WW','129525','LEVANTE240-BKCWW','CL-F019-PL12RE-A','CL-F042-PL12SW-A','CL-P022-AL12BU-A','CL-P022-AL12RE-A','CL-O004-GROSGM-A','CLW0224-B','CL-W007-PL12BL-A','CL-W108-PL12SW-A','CL-W150-PL14RE-A','CL-W138-PL14RE-A','CL-W157-PL12SW-A','cl-w232-pl12sw-a','NA-0921A','NA-0921R','NA-0919V','NA-0919A','NA-0919R','NA-0920A','NA-0920R','NA-0922','NA-0921V','NA-0920V','CW-9060028-WW','196847-20','703242','FKG400','412-AALK','FS1200','WC1200','WC2400','AC1200','VENTO120ARGB-BKCWW','VENTO120-BKCWW','LEVANTE360-BKCWW','CL-F081-PL20SW-A','CL-F080-PL14SW-A','CL-F055-PL12WT-A','CL-W232-PL12SW-B','CLW0222-B','CL-P065-AL12SW-A','CL-P064-AL12SW-A','90RC0030-M0UAY0','90RC0062-M0AAY0','90RC0020-M0UAY0','90RC0091-M0AAY0','ROG STRIX LC 360','CO-9050071-WW','CW-9060042-WW/RF','CW-9060028-BULK','CO-9050092-WW/RF','CO-9050072-WW/RF','CW-9060032-WW/RF','CW-9060038-WW/RF','CW-9060039-WW/RF','TP-100','963005','136902','TP200','TP300','BR-931311','BR-931328','BR-931335','BR-931342','YCF-3RGB-01','YCT-050720B','YCT-050720R','XZCO400B','XZVE100B','400-HY-CL24-V1','400-HY-CL28-V1','NA-0614','NA-0615','NA-0616','NA-0617','ROG STRIX LC II 240 ARGB','FG400','LQG500-BK','LCQ600-BK'];

        $skus = DB::table('productos')->select('id','sku')->where('estatus','Activo')->orderBy('id', 'DESC')->get()->toArray();
        // $skus = (array) $data;
        // return view('importar', compact('data'));
        // print_r($skus[0]->sku);

        // for($i=0;$i<sizeof($skus);$i++){
            // print_r($skus[$i]->sku);
            // print_r($url);
        // }
        
        // print_r(sizeof($skus));
        $client = new Client();   
        for($i=0;$i<sizeof($skus)-1;$i++){
            $sku = $skus[$i]->sku;
            if($sku==""){
                $sku="NOEXISTE";
                print_r($skus[$i]->id);
            }
            $url = "https://www.abasteo.mx/api/v0.1/catalog/filter?type=search&id=".$sku;
            $res = $client->request('GET', $url);
            $result = $res->getBody();
            $data = json_decode($result, true);
            print_r($sku);
            print_r(" - ".$data['price'][0]);
            print_r("\n");
            // echo "-";
            // print_r($url);
        }

        // $client = new Client();
        // $res = $client->request('GET', 'https://www.abasteo.mx/api/v0.1/catalog/filter?type=search&id=VENTO120-BKCW');
        // $result = $res->getBody();
        // $data = json_decode($result, true);
        // print_r($data['price']);
        // return view('sections.client.index')->with('clients', $clientes['items']);
        
    }
}
