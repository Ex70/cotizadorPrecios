<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
        



        $client = new Client();
        $res = $client->request('GET', 'https://www.abasteo.mx/api/v0.1/catalog/filter?type=search&id=VENTO120-BKCW');
        $result = $res->getBody();
        $data = json_decode($result, true);
        print_r($data['price']);
        // return view('sections.client.index')->with('clients', $clientes['items']);
        
    }
}
