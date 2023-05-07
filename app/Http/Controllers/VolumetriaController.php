<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Volumetria;
use Illuminate\Http\Request;

class VolumetriaController extends Controller
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

    public function agregarVolumetrias(){
        $ct = new CTConnect();
        $productos=Producto::where('estatus','Activo')->get();
        // dd(sizeof($productos));
        for ($i = 0; $i < sizeof($productos); $i++) {
            $volumenes=$ct->volumen($productos[$i]['clave_ct']);
            // dd($productos[$i]['clave_ct']);
            $producto = Volumetria::updateOrCreate(
                ['clave_ct' => $productos[$i]['clave_ct']],
                [
                    'peso' => !empty($volumenes['peso']) ? $volumenes['peso']:NULL,
                    'largo' => !empty($volumenes['largo']) ? $volumenes['largo']:NULL,
                    'alto' => !empty($volumenes['alto']) ? $volumenes['alto']:NULL,
                    'ancho' => !empty($volumenes['ancho']) ? $volumenes['ancho']:NULL,
                    'upc' => !empty($volumenes['UPC']) ? $volumenes['UPC']:NULL,
                    'ean' => !empty($volumenes['EAN']) ? $volumenes['EAN']:NULL
                ]
            );
        }
        dd("Terminado");
    }
}
