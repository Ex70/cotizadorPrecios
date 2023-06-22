<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Subcategoria;
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
        set_time_limit(0);
        $data['productos'] = Producto::join('woocommerce','woocommerce.clave_ct','=','productos.clave_ct')
            ->join('margenes_por_producto','margenes_por_producto.clave_ct','=','productos.clave_ct')
            ->where('productos.estatus','Activo')
            ->where('productos.existencias','>',0)
            ->orderBy('productos.id')
            ->get([
                'woocommerce.idWP',
                'productos.clave_ct',
                'productos.precio_unitario',
                'margenes_por_producto.margen_utilidad'
            ]
        );
        // dd(sizeof($data['productos']));
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            $prueba = Woocommerce::updateOrCreate(
                ['clave_ct'=>$data['productos'][$i]['clave_ct']],
                ['precio_venta'=>number_format($data['productos'][$i]['precio_unitario']*(1+$data['productos'][$i]['margen_utilidad']),2, '.', '')],
            );
        }
        dd("Listo");
    }

    public function preciosPromociones(){
        set_time_limit(0);
        $data['productos'] = Producto::Join('promociones','productos.clave_ct','=','promociones.clave_ct')
            ->join('woocommerce','woocommerce.clave_ct','=','promociones.clave_ct')
            ->join('margenes_por_producto','margenes_por_producto.clave_ct','=','promociones.clave_ct')
            ->where('productos.estatus','Activo')
            ->where('productos.existencias','>',0)
            ->whereMonth('promociones.updated_at',date('m'))
            ->whereDay('promociones.updated_at',date('d'))
            ->orderBy('productos.id')
            ->get([
                'woocommerce.idWP',
                'productos.clave_ct',
                'productos.precio_unitario',
                'margenes_por_producto.margen_utilidad',
                'promociones.descuento',
                'promociones.fecha_inicio',
                'promociones.fecha_fin'
            ]
        );
        dd(count($data['productos']));
        // for ($i = 0; $i < 1; $i++) {
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            $prueba = Woocommerce::updateOrCreate(
                ['clave_ct'=>$data['productos'][$i]['clave_ct']],
                ['precio_venta'=>number_format($data['productos'][$i]['precio_unitario']*(1+$data['productos'][$i]['margen_utilidad']),2, '.', ''),
                'precio_venta_rebajado'=>number_format($data['productos'][$i]['precio_unitario']*((1)-($data['productos'][$i]['descuento']/100))*(1+$data['productos'][$i]['margen_utilidad']),2, '.', ''),
                'fecha_inicio'=>$data['productos'][$i]['fecha_inicio'],
                'fecha_fin'=>$data['productos'][$i]['fecha_fin']]
            );
            // dd($prueba);
        }
        // dd(sizeof($data['productos']));
        dd("Listo");
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
        set_time_limit(0);
        $fechaR = date('Y')."-".date('m')."-".date('d');
        $woocommerce = new WooClient(
            'https://ehstecnologias.com.mx/',
            'ck_209a05b01fc07d7b4d54c05383b048f9d58c075f',
            'cs_0ef9dc123f35a8b70a76317e30a598332dcd01c6',
            [
                'version' => 'wc/v3',
                'timeout' => 800,
                'query_string_auth' => true,
                'verify_ssl' => false
            ]
        );
        for ($j = 1; $j < 3; $j++) {
            $params = [
                'per_page'=>100,
                'page'=>$j
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
        }
        dd("Listo");
    }

    public function actualizarExistencias(Request $request){
        set_time_limit(0);
        $woocommerce = new WooClient(
            'https://www.ehstecnologias.com.mx/',
            'ck_209a05b01fc07d7b4d54c05383b048f9d58c075f',
            'cs_0ef9dc123f35a8b70a76317e30a598332dcd01c6',
            [
                'version' => 'wc/v3',
                'timeout' => 800,
                'query_string_auth' => true,
                'verify_ssl' => false
            ]
        );
        $APICT = new CTConnect;
        $test = $request->get('filtro1');
        $test2 = $request->get('filtro2');
        $test3 = $request->get('filtro3');
        if ($test3 == 'z') {
            $data['productos'] = Producto::join('woocommerce','woocommerce.clave_ct','=','productos.clave_ct')
                ->where('productos.existencias','>',0)
                ->where('categoria_id', $test)
                ->where('subcategoria_id', $test2)
                ->orderBy('productos.id')
                ->get([
                    'woocommerce.idWP',
                    'productos.clave_ct',
                ]
            );
            // $data['productos'] = Producto::where('categoria_id', $test)->where('subcategoria_id', $test2)->where('estatus', 'Activo')->get();
        } else {
            $data['productos'] = Producto::join('woocommerce','woocommerce.clave_ct','=','productos.clave_ct')
            ->where('productos.existencias','>',0)
            ->where('categoria_id', $test)
            ->where('subcategoria_id', $test2)
            ->where('marca_id', $test3)
            ->orderBy('productos.id')
            ->get([
                'woocommerce.idWP',
                'productos.clave_ct',
            ]);
            // $data['productos'] = Producto::where('categoria_id', $test)->where('subcategoria_id', $test2)->where('marca_id', $test3)->where('estatus', 'Activo')->get();
        }
        // for ($i = 0; $i < 2; $i++) {
        for ($i = 0; $i < sizeof($data['productos']); $i++) {
            $dataWP = [
                'stock_quantity' => $APICT->existenciaProductoWP($data['productos'][$i]['clave_ct']),
            ];
            $producto = $woocommerce->put('products/'.$data['productos'][$i]['idWP'], $dataWP);
        }
        $data['categorias'] = Categoria::distinct('nombre')->where('nombre','not like',"% - 2%")->orderBy('nombre')->get();
        $data['marcas'] = Marca::distinct('nombre')->orderBy('nombre')->get();
        $data['subcategorias'] = Subcategoria::distinct('nombre')->orderBy('nombre')->get();
        return view('wp.existencias', compact('data'));
        // dd($data['productos'][0]);
        // dd("Terminado");
    }
}
