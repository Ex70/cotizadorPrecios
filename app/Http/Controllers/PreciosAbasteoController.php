<?php

namespace App\Http\Controllers;

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
        // $client = new Client();
        // $res = $client->request('GET', 'https://www.abasteo.mx/api/v0.1/catalog/filter?type=search&id=VENTO120-BKCW');
        // $result = $res->getBody();
        // $data = json_decode($result, true);
        // dd($data['price']);

        $client = new Client();
        for($i=0;$i<sizeof($productos)-1;$i++){
            $sku = $productos[$i]->sku;
            if($sku==""){
                $sku="NOEXISTE";
                print_r($productos[$i]->id);
            }
            $url = "https://www.abasteo.mx/api/v0.1/catalog/filter?type=search&id=".$sku;
            $res = $client->request('GET', $url);
            $result = $res->getBody();
            $data = json_decode($result, true);
            // print_r($sku);
            // print_r(" - ".$data['price'][0]);
            // print_r("\n");
            // echo "-";
            // print_r($url);
            $precios[$i]= $data['price'][0];
        }
        return $precios;
    }
}
