<?php

namespace App\Http\Controllers;

use App\Models\Pcmayoreo;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PcMayoreoController extends Controller
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

    public function cotizar(){
        set_time_limit(0);
        $client = new Client();
        // $ch = curl_init("https://www.pchmayoreo.com/instantsearch/ajax/result/?q=");
        // curl_setopt($ch, CURLOPT_CAPATH, "C:\xampp\php\cacert.pem");

        // $guzzle = new \GuzzleHttp\Client();
        // $guzzle->setDefaultOption('verify', 'C:\xampp\php\cacert.pem');
        
        //$client->request('GET', '/', ['verify' => true]);
        //Use a custom SSL certificate on disk.
        //$client->request('GET', '/', ['verify' => 'C:\xampp\php\cacert.pem']);
        //Disable validation entirely (don't do this!).
        //$client->request('GET', '/', ['verify' => false]);
        
        //for($i=0;$i<sizeof($productos)-1;$i++){
        for($i=0;$i<5;$i++){
            //$sku = $productos[$i]->sku;
            $sku = "WKGP-002";
            //$clave_ct = $productos[$i]->clave_ct;
            if($sku==""){
                $sku="NOEXISTE";
            }
            //$url = "http://www.pchmayoreo.com/instantsearch/ajax/result/?q=".$sku;
            $url = "http://www.pchmayoreo.com/catalogsearch/result?q=".$sku;
            //dd($url);
            $res = $client->request('GET', $url);
            $result = $res->getBody();
            $data = json_decode($result, true);
            if(empty($data['results']['articleIds'])){
                $precios[$i]= 0;
                $data2 = "";
            }else{
                // $id = $data['results']['articleIds'][0];
                // $url = "https://www.cyberpuerta.mx/widget.php?cl=cpmobile_ajax&fnc=getArticles&skipSession=1&ids%5B%5D=".$id;
                // $res = $client->request('GET', $url);
                // $result = $res->getBody();
                // $data2 = json_decode($result, true);
                dd('Error');
            }
            if(!empty($data2['articles'])){
                $precios[$i]= $data2['articles'][0]['price'];
                dd($precios[$i]);
            }else{
                $precios[$i]= 0;
            }
            $productoCP = PcMayoreo::updateOrCreate(
                ['sku'=>$sku, 'clave_ct'=>$clave_ct],
                ['precio_unitario'=>$precios[$i]]
            );
        }
        return $precios;
    }
}
