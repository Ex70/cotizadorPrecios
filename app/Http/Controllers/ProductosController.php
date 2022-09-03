<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Subcategoria;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $data['categorias'] = Categoria::distinct('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->get();
        // $data['marcas'] = Producto::distinct('nombre')
        // ->join('marcas','marcas.id','=','productos.marca_id')
        // ->where('estatus','Activo')
        // ->get();
        if($request->has('filtro1') && $request->has('filtro2')){
            $productos['data'] = Producto::where('productos.categoria_id',$request->get('filtro1'))
                    ->where('productos.subcategoria_id',$request->get('filtro2'))
                    ->where('productos.marca_id',$request->get('filtro3'))
                    ->where('productos.estatus','Activo')->get();
            $existencias = new CTConnect;
            $existencias->existencias($productos['data']);
            // dd(sizeof($productos['data']));
            $data['productos'] = Producto::join('categorias','categorias.id','=','productos.categoria_id')
                ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
                ->join('marcas','marcas.id','=','productos.marca_id')
                ->join('abasteo','abasteo.sku','=','productos.sku')
                ->join('mipc','mipc.sku','=','productos.sku')
                ->join('zegucom','zegucom.sku','=','productos.sku')
                ->where('productos.categoria_id',$request->get('filtro1'))
                ->where('productos.subcategoria_id',$request->get('filtro2'))
                ->where('productos.marca_id',$request->get('filtro3'))
                ->where('productos.estatus','Activo')
                ->get([
                    'productos.clave_ct',
                    'productos.existencias',
                    'productos.sku',
                    'productos.precio_unitario as precioct',
                    'abasteo.precio_unitario as abasteo',
                    'mipc.precio_unitario as mipc',
                    'zegucom.precio_unitario as zegucom',
                    'categorias.nombre as categoria',
                    'subcategorias.nombre as subcategoria',
                    'marcas.nombre as marca'
                ]);
                // dd(sizeof($data['productos']));
                // $existencias = new CTConnect;
                // $data['existencias'] = $existencias->existencias($data['productos']);
        }else{
            $data['productos'] = [];
        }
        // dd(sizeof($data['productos']));
        return view('productos.index', compact('data'));
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

    public function limpieza(){
        Producto::where('id','>',0)->update([
            'estatus' => 'Descontinuado'
        ]);
    }
}
