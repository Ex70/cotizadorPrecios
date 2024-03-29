<?php

namespace App\Http\Controllers;

use App\Models\Abasteo;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PreciosAbasteoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cotizar($productos){
        set_time_limit(0);
        $client = new Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
        // $guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
// $client->setHttpClient($client);
        for($i=0;$i<sizeof($productos);$i++){
            $sku = $productos[$i]->sku;
            $clave_ct = $productos[$i]->clave_ct;
            if($sku==""){
                $sku="NOEXISTE";
                print_r($productos[$i]->id);
            }
            $headers = ['Content-Type' => 'application/json'];
            $url = "https://www.abasteo.mx/api/v0.1/catalog/filter?type=search&id=".$sku;
            $res = $client->request('GET', $url, ['headers' => [
                'Accept' => 'application/json',
        'Content-Type' => 'application/json',
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36"
            ]]);
            // $res = $client->request('GET', $url);
            $result = $res->getBody();
            $data = json_decode($result, true);
            $precios[$i]= $data['price'][0];
            $productoAbasteo = Abasteo::updateOrCreate(
                ['sku'=>$sku, 'clave_ct'=>$clave_ct],
                ['precio_unitario'=>$data['price'][0]]
            );
        }
        return $precios;
    }
}
