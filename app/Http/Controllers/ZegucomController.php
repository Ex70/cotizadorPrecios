<?php

namespace App\Http\Controllers;

use App\Models\Zegucom;
use Exception;
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
 
    public function cotizar($productos){
        set_time_limit(0);
        $remove = array(" ","  ","   ","    ", "(", ")", "$", "*", "/",",","IVA","Incluido","Desde");
        $client = new Client();
        for($i=0;$i<sizeof($productos);$i++){
            $sku = $productos[$i]->sku;
            $clave_ct = $productos[$i]->clave_ct;
            if($sku==""){
                $sku="NOEXISTE";
            }
            // $sku="981-000889";
            // dd($sku);
            // $website = $client->request('GET', 'https://www.zegucom.com.mx/?cons='.$sku.'&mod=search&reg=1');
            // $website = $client->request('GET', 'https://www.zegucom.com.mx/productos/search?search='.$sku.'');
            // $website = $client->request('GET', 'https://www.zegucom.com.mx/productos/search?search=CS400C-5BBC');
            // $result = $website->filter('.search-price-now > .search-price-now-value ');
            // $result = $website->filter('.text-darken-4');
            try {
                $website = $client->request('GET', 'https://www.zegucom.com.mx/productos/search?search='.$sku.'');
                $result = $website->filter('.text-darken-4');
                // dd($result);
                $precios[$i] = $result->count() ? str_replace($remove, "", $website->filter('.text-darken-4')->eq(1)->first()->text()) : $precios[$i] = 0;
                $productoZegucom = Zegucom::updateOrCreate(
                    ['sku'=>$sku, 'clave_ct'=>$clave_ct],
                    ['precio_unitario'=>$precios[$i]]
                );
            }catch(Exception $e){
                $precios[$i]=0;
                // break;
                // dd("Error");

            }
            // $result = $website->filter('.search-price-now-value');
            // dd($result->count());
            // if(!$result->count()){
            //     dd($result);
            // }
            // dd(str_replace($remove, "", $website->filter('.text-darken-4')->first()->text()));
            // $result = $website->filter('.price-text > .result-price-search');
            // $precios[$i] = $result->count() ? str_replace($remove, "", $website->filter('.price-text > .result-price-search')->first()->text()) : $precios[$i] = 0;
            //              $precios[$i] = $result->count() ? str_replace($remove, "", $website->filter('.text-darken-4')->eq(1)->first()->text()) : $precios[$i] = 0;
            // dd($precios[$i]);
            // dd($productoZegucom);
        }
        // dd($precios);
        return $precios;
        // $data['precios'] = $precios;
        // dd($data['precios']);
        // $data['categoria'] = $request->get('filtro1');
        // $data['subcategoria'] = $request->get('filtro2');
        // $data['productos'] = DB::table('productos')->select('id','descripcion')->where('estatus','Activo')->where('categoria',$test)->where('subcategoria',$test2)->orderBy('id', 'ASC')->get()->toArray();
        // $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        // $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        // return view('filtrosmipc',compact('data'));
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
