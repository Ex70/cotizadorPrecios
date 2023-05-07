<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Woocommerce;
use Illuminate\Http\Request;
use Automattic\WooCommerce\Client as WooClient;

class WoocommerceController extends Controller
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

    public function preciosVenta(){
        $data['productos'] = Woocommerce::Join('productos', 'productos.clave_ct', '=', 'woocommerce.clave_ct')
            ->leftJoin('promociones', 'promociones.clave_ct', '=', 'productos.clave_ct')
            ->join('margenes_por_producto','margenes_por_producto.clave_ct','=','woocommerce.clave_ct')
            ->where('productos.estatus', 'Activo')
            ->whereNull('promociones.clave_ct')
            ->get([
                'woocommerce.idWP',
                'productos.clave_ct',
                'woocommerce.precio_venta',
                'woocommerce.precio_venta_rebajado',
                'woocommerce.fecha_inicio',
                'woocommerce.fecha_fin',
                'productos.precio_unitario',
                'margenes_por_producto.margen_utilidad'
            ]
        );
        dd(sizeof($data['productos']));
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            $prueba = Woocommerce::updateOrCreate(
                ['clave_ct'=>$data['productos'][$i]['clave_ct']],
                ['precio_venta'=>number_format($data['productos'][$i]['precio_unitario']*(1+$data['productos'][$i]['margen_utilidad']),2, '.', '')]
            );
        }
    }

    public function woocommerce()
    {
        set_time_limit(0);
        $woocommerce = new WooClient(
            'http://ehstecnologias.com.mx/',
            'ck_209a05b01fc07d7b4d54c05383b048f9d58c075f',
            'cs_0ef9dc123f35a8b70a76317e30a598332dcd01c6',
            [
                'version' => 'wc/v3',
                'timeout' => 800
            ]
        );
        $params = [
            'per_page'=>100,
            'page'=>36
        ];
        $data['woocommerce'] = $woocommerce->get('products',$params);
        for ($i = 0; $i < sizeof($data['woocommerce']); $i++) {
            $ids = Woocommerce::updateOrCreate(
                ['idWP' => $data['woocommerce'][$i]->id],
                [
                'idWP' => $data['woocommerce'][$i]->id,
                'clave_ct' => $data['woocommerce'][$i]->sku,
                ]
            );
        }
        dd("Terminado");
    }

    public function actualizarInventario(){
        $params = [
            'per_page'=>100,
            'page'=>7
        ];
        $data['woocommerce'] = $woocommerce->get('products',$params);
        for ($i = 0; $i < sizeof($data['woocommerce']); $i++) {
            $ids = Woocommerce::updateOrCreate(
                ['idWP' => $data['woocommerce'][$i]->id],
                [
                    'idWP' => $data['woocommerce'][$i]->id,
                    'clave_ct' => $data['woocommerce'][$i]->sku,
                ]
            );
        }
        dd("Listo");
    }
}
