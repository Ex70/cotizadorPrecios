<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Support\Facades\DB;

class ZegucomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtroszegucom',compact('data'));
    }

    public function cotizar(Request $request){
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $skus = DB::table('productos')->select('id','sku')->where('id','>=','37501')->where('id','<=','40000')->orderBy('id', 'ASC')->get()->toArray();
        $client = new Client();
        $precios = [];
        for($i=0;$i<sizeof($skus);$i++){
            $sku = $skus[$i]->sku;
            if($sku==""){
                $sku="NOEXISTE";
            }
            // try{
                $website = $client->request('GET', 'https://www.zegucom.com.mx/?cons='.$sku.'&mod=search&reg=1');
                // $precios[$i] = $website->count() ? $precios[$i] = 1 : $precios[$i] = 0;
                $result = $website->filter('.price-text > .result-price-search');
                // $result = $website->filter('.emproduct_right_price_left > .price')->first()->text();
                $precios[$i] = $result->count() ? $website->filter('.price-text > .result-price-search')->first()->text() : $precios[$i] = 0;
                
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
}
