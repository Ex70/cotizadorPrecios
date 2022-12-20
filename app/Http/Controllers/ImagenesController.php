<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ImagenesController extends Controller
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

    public function obtener(){
        $sku = "SKU001";
        $url = "http://www.google.co.in/intl/en_com/images/srpr/logo1w.png";
        $contents = file_get_contents($url);
        // $name = $sku;
        $datos = pathinfo($url);
        $nombre = $sku.".".$datos['extension'];
        // dd($nombre);
        // dd($datos['extension']);
        // dd(pathinfo($url));
        // dd(substr($url, $sku.$datos['extension']));
        // $name = 'productos/'.substr($url, strrpos($url, '/') + 1);
        // dd($name);
        Storage::put('productos/'.$nombre, $contents);
        dd($nombre);



        set_time_limit(0);
        $remove = array(" ","  ","   ","    ", "(", ")", "$", "*", "/",",","IVA","Incluido");
        $client = new Client();
        for($i=0;$i<2;$i++){
            // $sku = $productos[$i]->sku;
            // $clave_ct = $productos[$i]->clave_ct;
            // if($sku==""){
            //     $sku="NOEXISTE";
            // }
            $website = $client->request('GET', 'https://www.barcodelookup.com/4710886162469');
            // $website = $client->request('GET', 'https://www.zegucom.com.mx/?cons='.$sku.'&mod=search&reg=1');
            $result = $website->filter('body')->first();
            dd($result->text());
            $imagenes[$i] = $result->count() ? $website->filter('.col-50 > #largeProductImage')->first()->text() : $imagenes[$i] = 0;
            dd($imagenes[$i]);
            // $imagenes[$i] = $result->count() ? str_replace($remove, "", $website->filter('.price-text > .result-price-search')->first()->text()) : $imagenes[$i] = 0;
            // $productoZegucom = Zegucom::updateOrCreate(
            //     ['sku'=>$sku, 'clave_ct'=>$clave_ct],
            //     ['precio_unitario'=>$imagenes[$i]]
            // );
        }
        // dd($imagenes);
        return $imagenes;
    }
}
