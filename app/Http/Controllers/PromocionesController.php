<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $promociones = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')->get();
        return view('promociones.vigentes', compact('promociones'));
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
        $promociones = DB::select("SELECT productos.clave_ct, productos.sku, categorias.nombre AS categoria, subcategorias.nombre AS subcategoria, promociones.descuento, promociones.fecha_fin, promociones.updated_at FROM promociones INNER JOIN productos ON promociones.clave_ct = productos.clave_ct INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategorias.id WHERE productos.estatus = 'Activo' AND (promociones.consulta = NULL OR promociones.consulta = '".$fecha."') AND EXTRACT(DAY FROM promociones.updated_at)= '".date('d')."';");
        //dd($promociones);
        
        Promocion::whereNull('consulta')->update([
            'consulta' => $fecha
        ]);
        Promocion::whereMonth('updated_at','<',date('m'))->whereYear('created_at',date('Y'))->delete();
        return view('promociones.vigentes', compact('promociones'));
    }

    public function vigentes(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        // dd($fecha);
        $promociones = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->where('productos.estatus','Activo')
            //->whereBetween('promociones.fecha_fin',[today(), '2022-11-30'])
            ->whereDate('promociones.fecha_fin','>=',$fecha)
            // ->whereMonth('promociones.fecha_fin','>=', date('d'))
            // ->whereDay('promociones.fecha_fin','>=', date('m'))
            ->orderBy("promociones.created_at","desc")
            ->get();
        return view('promociones.vigentes', compact('promociones'));
    }

    public function delMes(){
        $promociones = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->where('productos.estatus','Activo')
            ->whereMonth('promociones.fecha_fin','>=', date('m'))
            ->get();
        return view('promociones.vigentes', compact('promociones'));
    }

    public function vencidas(){
        $fecha = date('Y')."-".date('m')."-".date('d')-1;
        //dd($fecha);
        $promociones = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->where('productos.estatus','Activo')
            ->whereDate('promociones.fecha_fin','=', $fecha)
            ->get();
        return view('promociones.vigentes', compact('promociones'));
    }

}