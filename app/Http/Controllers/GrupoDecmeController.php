<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use App\Models\totalesDecme;

class GrupoDecmeController extends Controller
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
        return view('filtrosgrupodecme',compact('data'));
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

    public function llenadoJSON(){
        $client = new Client();
        $precios = [];
        // $skus=2;
        for($i=0;$i<41;$i++){
            // $url = "http://www.grupo-decme.myshopify.com/products.json?limit=250&page=".$i;
            $url = "http://www.grupo-decme.myshopify.com/products.json?limit=250&page=1";
            $res = $client->request('GET', $url);
            $result = $res->getBody();
            $data = json_decode($result, true);
            $productos = $data['products'];
            dd($data);
            for($j=0;$j<sizeof($productos)-1;$j++){
                totalesDecme::create([
                    "title" => $productos[$j]['title'],
                    "sku" => $productos[$j]['variants'][0]['sku'],
                    "precio" => $productos[$j]['variants'][0]['price']
                ]);
            }
        }
        // dd($data['products']);

        // dd($data['precios']);
        
        $data['categoria'] = $request->get('filtro1');
        $data['subcategoria'] = $request->get('filtro2');
        $data['productos'] = DB::table('productos')->select('id','descripcion')->where('estatus','Activo')->where('categoria',$test)->where('subcategoria',$test2)->orderBy('id', 'ASC')->get()->toArray();
        $data['categorias'] = DB::table('productos')->distinct()->get(['categoria']);
        $data['subcategorias'] = DB::table('productos')->distinct()->get(['subcategoria']);
        return view('filtrosmipc',compact('data'));
    }
}
