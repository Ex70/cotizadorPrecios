<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\fmd;
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
                    ->where('productos.estatus','Activo')
                    ->where('productos.existencias','>',0)->get();
            //$productos['data'] = Producto::all();
            // $existencias = new CTConnect;
            // $existencias->existencias($productos['data']);
            $data['totalSubCat'] = Producto::where('subcategoria_id',$request->get('filtro2'))->where('estatus','Activo')->sum('existencias');
            $data['totalCat'] = Producto::where('categoria_id',$request->get('filtro1'))->where('estatus','Activo')->sum('existencias');
            $data['totalMarca'] = Producto::where('marca_id',$request->get('filtro3'))->where('estatus','Activo')->sum('existencias');
            $data['totalFiltro'] = Producto::where('marca_id',$request->get('filtro3'))->where('subcategoria_id',$request->get('filtro2'))->where('categoria_id',$request->get('filtro1'))->where('estatus','Activo')->sum('existencias');
            // dd($data['totalFiltro']);
            $data['productos'] = Producto::join('categorias','categorias.id','=','productos.categoria_id')
                ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
                ->join('marcas','marcas.id','=','productos.marca_id')
                ->join('abasteo','abasteo.sku','=','productos.sku')
                ->join('mipc','mipc.sku','=','productos.sku')
                ->join('zegucom','zegucom.sku','=','productos.sku')
                ->where('productos.categoria_id',$request->get('filtro1'))
                ->where('productos.subcategoria_id',$request->get('filtro2'))
                ->where('productos.marca_id',$request->get('filtro3'))
                ->where('productos.existencias','>',0)
                ->where('productos.estatus','Activo')
                ->orderBy('productos.existencias')
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
        // dd($data['productos']);
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

    public function existencias(){
        $productos['data'] = Producto::where('estatus','Activo')->get();
        $existencias = new CTConnect;
        $existencias->existencias($productos['data']);
    }

    public function google_my_business(){
        $data['productos'] = Producto::join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
            //->join('tops_mensuales','tops_mensuales.clave_ct','=','productos.clave_ct')
            ->where('productos.estatus','Activo')
            ->where('productos.existencias','>','100')
            ->orderBy('productos.clave_ct')
            ->get([
                'productos.clave_ct',
                'subcategorias.nombre as subcategoria',
                'productos.nombre',
                'productos.descripcion_corta',
                'productos.enlace'
            ]);
            // dd($data['productos']);
        return view('reportes.google-my-business', compact('data'));
    }

    public function imagen(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
        ->whereDay('productos.created_at', date('d'))->whereMonth('productos.created_at', date('m'))
        ->where('productos.estatus','Activo')
        ->orderBy('productos.clave_ct')
        ->get([
            'productos.clave_ct',
            'subcategorias.nombre as subcategoria',
            'productos.nombre',
            'productos.descripcion_corta',
            'productos.imagen',
            'productos.created_at'
        ]);
            // dd($data['productos']);
        return view('reportes.fmd', compact('data'));
    }

    public function nuevos_gmb(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['productos'] = Producto::join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
        ->whereDay('productos.created_at', date('d'))->whereMonth('productos.created_at', date('m'))
        ->where('productos.estatus','Activo')
        ->where('productos.existencias','>','100')
        ->orderBy('productos.clave_ct')
        ->get([
            'productos.clave_ct',
            'subcategorias.nombre as subcategoria',
            'productos.nombre',
            'productos.descripcion_corta',
            'productos.enlace',
        ]);
            // dd($data['productos']);
        return view('reportes.google-my-business', compact('data'));
    }

    public function cartas(Request $request){
        $clave = $request->clavect;
        $data['productos'] = Producto::join('categorias', 'categorias.id', '=', 'productos.categoria_id')
                ->join('subcategorias', 'subcategorias.id', '=', 'productos.subcategoria_id')
                ->join('marcas', 'marcas.id', '=', 'productos.marca_id')
                ->where('productos.clave_ct', '=', $clave)
                ->where('productos.estatus', 'Activo')
                ->get(
                    [
                    'productos.clave_ct',
                    'productos.nombre',
                    'categorias.nombre as categoria',
                    'subcategorias.nombre as subcategoria',
                    'marcas.nombre as marca',
                    'productos.enlace',
                    'productos.imagen',
                    'productos.existencias'
                ]
            );
        //dd($data['productos']);
        if ($request->has('clavect')) {
        }
        return view('productos.cartaProductos', compact('data'));
    }
}

