<?php

namespace App\Http\Controllers;

use App\Models\Cyberpuerta;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CyberPuertaController extends Controller
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
        $client = new Client();
        for($i=0;$i<sizeof($productos)-1;$i++){
            $sku = $productos[$i]->sku;
            $clave_ct = $productos[$i]->clave_ct;
            if($sku==""){
                $sku="NOEXISTE";
            }
            $url = "https://www.cyberpuerta.mx/widget.php?cl=cpmobile_ajax&fnc=getSearchSuggest&q=".$sku."&userEmail=&skipSession=1";
            $res = $client->request('GET', $url);
            $result = $res->getBody();
            $data = json_decode($result, true);
            if(empty($data['results']['articleIds'])){
                $precios[$i]= 0;
                $data2 = "";
            }else{
                $id = $data['results']['articleIds'][0];
                $url = "https://www.cyberpuerta.mx/widget.php?cl=cpmobile_ajax&fnc=getArticles&skipSession=1&ids%5B%5D=".$id;
                $res = $client->request('GET', $url);
                $result = $res->getBody();
                $data2 = json_decode($result, true);
            }
            if(!empty($data2['articles'])){
                $precios[$i]= $data2['articles'][0]['price'];
            }else{
                $precios[$i]= 0;
            }
            $productoCP = Cyberpuerta::updateOrCreate(
                ['sku'=>$sku, 'clave_ct'=>$clave_ct],
                ['precio_unitario'=>$precios[$i]]
            );
        }
        return $precios;
    }
}
