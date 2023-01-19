<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PromocionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $data['promociones'] = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
        ->join('categorias','categorias.id','=','productos.categoria_id')
        ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
        ->get(
            [
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'promociones.clave_ct',
                'productos.sku',
                'promociones.descuento',
                'promociones.fecha_fin'
                ]
        );
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - Todas las Ofertas (".$fechaR.")";
        return view('promociones.vigentes', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        //
    }

    public function nuevas(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        //dd($fecha);
        // dd($fecha);
        /*$promociones = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
        ->join('categorias','categorias.id','=','productos.categoria_id')
        ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
        ->where('productos.estatus','Activo')
        ->whereNull('promociones.consulta')
        ->whereDate('promociones.updated_at','=',$fecha)
        ->orderBy("promociones.fecha_fin","desc")
        ->get([
            'productos.clave_ct',
            'productos.sku',
            'categorias.nombre as categoria',
            'subcategorias.nombre as subcategoria',
            'promociones.descuento',
            'promociones.fecha_fin'
            ]);*/
        $data['promociones'] = DB::select("SELECT productos.clave_ct, productos.sku, categorias.nombre AS categoria, subcategorias.nombre AS subcategoria, promociones.descuento, promociones.fecha_fin, promociones.updated_at FROM promociones INNER JOIN productos ON promociones.clave_ct = productos.clave_ct INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategorias.id WHERE productos.estatus = 'Activo' AND (promociones.consulta = NULL OR promociones.consulta = '".$fecha."') AND EXTRACT(DAY FROM promociones.updated_at)= '".date('d')."';");
        //dd($promociones);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - Nuevas Ofertas (".$fechaR.")";
        //dd($data['titulo']);
        Promocion::whereNull('consulta')->update([
            'consulta' => $fecha
        ]);
        //dd($promociones[0]);
        Promocion::whereYear('created_at','<', date('Y'))->delete();

        Promocion::whereMonth('updated_at','<',date('m'))->whereYear('created_at', date('Y'))->delete();

        return view('promociones.vigentes', compact('data'));
    }

    public function vigentes(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        // dd($fecha);
        $data['promociones'] = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->join('categorias','categorias.id','=','productos.categoria_id')
            ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
            ->where('productos.estatus','Activo')
            //->whereBetween('promociones.fecha_fin',[today(), '2022-11-30'])
            ->whereDate('promociones.fecha_fin','>=',$fecha)
            // ->whereMonth('promociones.fecha_fin','>=', date('d'))
            // ->whereDay('promociones.fecha_fin','>=', date('m'))
            ->orderBy("promociones.created_at","desc")
            ->get(
                [
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'promociones.clave_ct',
                'productos.sku',
                'promociones.descuento',
                'promociones.fecha_fin'
                ]
            );
            $fechaR = date('d')."-".date('m')."-".date('Y');
            $data['titulo'] = "EHS - Ofertas Vigentes (".$fechaR.")";
        return view('promociones.vigentes', compact('data'));
    }

    public function delMes(){
        //dd(date('Y'));
        $data['promociones'] = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->join('categorias','categorias.id','=','productos.categoria_id')
            ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
            ->where('productos.estatus','Activo')
            ->whereYear('promociones.fecha_fin','=', date('Y'))
            ->whereMonth('promociones.fecha_fin','=', date('m'))
            ->get([
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'promociones.clave_ct',
                'productos.sku',
                'promociones.descuento',
                'promociones.fecha_fin'
                ]
            );
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - Ofertas del Mes (".$fechaR.")";
        return view('promociones.vigentes', compact('data'));
    }

    public function vencidas(){
        $fecha = date('Y')."-".date('m')."-".date('d')-1;
        //dd($fecha);
        $data['promociones'] = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
        ->join('categorias','categorias.id','=','productos.categoria_id')
        ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
        //->where('productos.estatus','Activo')
        ->whereDate('promociones.fecha_fin','<=', $fecha)
        //->whereDay('promociones.fecha_fin','<', date('d'))
        ->get([
            'categorias.nombre as categoria',
            'subcategorias.nombre as subcategoria',
            'promociones.clave_ct',
            'productos.sku',
            'promociones.descuento',
            'promociones.fecha_fin'
            ]
        );
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - Ofertas Vencidas (".$fechaR.")";
        return view('promociones.vigentes', compact('data'));
    }

    public function cartaPromociones(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        $prom = Producto::Join('promociones', 'productos.clave_ct', '=', 'promociones.clave_ct') 
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('subcategorias', 'productos.subcategoria_id', '=', 'subcategorias.id')
            ->join('marcas', 'productos.marca_id', '=', 'marcas.id')
            ->where('productos.estatus', 'Activo')
            ->where('productos.existencias', '>', 0)
            ->where('promociones.fecha_fin', '>=', $fecha)
            ->orderBy('promociones.descuento', 'desc')
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.enlace',
                'productos.imagen',
                'productos.existencias',
                'promociones.descuento as descuento',
                'promociones.fecha_fin as fecha_fin'
            ])
            ->toArray();
        $prom = $this->paginate($prom, 20);
        $prom->withPath('/Promociones/Cartas');
        return view('cartas.cartas', compact('prom'));  
    }

    public function paginate($items, $perPage = 20, $page = null){
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage ;
        $itemstoshow = array_slice($items , $offset , $perPage);
        return new LengthAwarePaginator($itemstoshow ,$total ,$perPage);
    }
    public function getFile($filename){
        // return $filename;
        $file=Storage::disk('productos')->get($filename);
        return (new Response($file, 200))
            ->header('Content-Type', 'image/jpeg');
    }
}

