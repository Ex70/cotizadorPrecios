<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use Illuminate\Http\Request;

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
        $promociones = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->where('productos.estatus','Activo')
            ->whereDate('promociones.created_at','=',$fecha)
            // ->whereMonth('promociones.fecha_fin','>=', date('d'))
            // ->whereDay('promociones.fecha_fin','>=', date('m'))
            ->orderBy("promociones.fecha_fin","desc")
            ->get();
        return view('promociones.vigentes', compact('promociones'));
    }

    public function vigentes(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        // dd($fecha);
        $promociones = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->where('productos.estatus','Activo')
            ->whereBetween('promociones.fecha_fin',[today(), '2022-11-30'])
            // ->whereMonth('promociones.fecha_fin','>=', date('d'))
            // ->whereDay('promociones.fecha_fin','>=', date('m'))
            ->orderBy("promociones.fecha_fin","desc")
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
