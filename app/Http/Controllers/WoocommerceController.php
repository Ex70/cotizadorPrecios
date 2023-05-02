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
}
