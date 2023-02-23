<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class ImagenesController extends Controller
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

    public function obtener(){
        // public function obtener($producto){
        set_time_limit(0);
        $faltantes = "";
        $producto=Producto::where('estatus','Activo')->where('id','>',40901)->where('id','<',43000)->whereNotNull('upc')->whereNotNull('ean')->orderBy('existencias','desc')->get();
        // $producto=Producto::where('estatus','Activo')->where('id','>',0)->whereNotNull('upc')->whereNotNull('ean')->where('id','<',5000)->orderBy('existencias','desc')->get();
        dd(count($producto));
        // dd(strlen($producto['ean']));
        for($i=0;$i<sizeof($producto);$i++){
            if(strlen($producto[$i]['ean']>0)){
                $busqueda = $producto[$i]['ean'];
            }else{
                if(strlen($producto[$i]['upc']>0)){
                    $busqueda = $producto[$i]['upc'];
                }else{
                    $busqueda="HOLA";
                    // break;
                }
            }
            // dd($busqueda);
            if($busqueda!="HOLA"){
                    // LEER DESDE API
                $client = new Client();
                // dd($busqueda);
                $headers = ['Content-Type' => 'application/json'];
                $url = "https://api.barcodelookup.com/v3/products?barcode=".$busqueda."&key=u30gi6v08sqi3qw0gstd7ovk3fqfrb";
                try {
                    $res = $client->request('GET', $url, ['headers' => [
                        'Accept' => 'application/json',
                        'http_errors' => false,
                        'Content-Type' => 'application/json',
                        'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36"
                    ]]);
                    $result = $res->getBody();
                    $dataImg = json_decode($result, true);
                    $nombreJSON = $producto[$i]['clave_ct'].".json";
                    $newJsonString = json_encode($dataImg, JSON_PRETTY_PRINT);
                    Storage::disk('json')->put($nombreJSON, $newJsonString);
                    $valor = $dataImg['products'][0]['barcode_formats'];
                    $stringSeparado = explode(",",$valor);
                    for($j=0;$j<count($stringSeparado);$j++){
                        $pos=strpos($stringSeparado[$j],"UPC-A");
                        if(is_int($pos)){
                            $datos['upc']=str_replace(" ","",str_replace("UPC-A","",$stringSeparado[$j]));
                        }else{
                            $datos['upc'] = (!isset($datos['upc'])) ? NULL : $datos['upc'];
                            // $datos['upc'] = NULL;
                            $pos=strpos($stringSeparado[$j],"EAN-13");
                            if($pos>=0){
                                $datos['ean']=str_replace(" ","",str_replace("EAN-13","",$stringSeparado[$j]));
                            }
                        }
                    }
                    $productoAPI = Producto::updateOrCreate(
                        ['clave_ct'=>$producto[$i]['clave_ct']],
                        [
                            'upc'=>(is_null($producto[$i]['upc'])) ? $datos['upc'] : $datos['upc'],
                            'ean'=>(is_null($producto[$i]['ean'])) ? $datos['ean'] : $datos['ean'],
                            'google_cat'=>$dataImg['products'][0]['category']
                        ]
                    );
                    for($h=0;$h<sizeof($dataImg['products'][0]['images']);$h++){
                        $urlImagen = $dataImg['products'][0]['images'][$h];
                        $contents = file_get_contents($urlImagen);
                        $datos = pathinfo($urlImagen);
                        $nombre = $producto[$i]['clave_ct']."-".$h.".".$datos['extension'];
                        $ruta = 'productos/'.$nombre;
                        // $urlImagen->move(public_path('/',$nombre));
                        Storage::disk('public')->put($ruta, $contents);
                        Storage::put($ruta, $contents);
                        $data=$nombre;
                        // list($width, $height, $type, $attr) = getimagesize($dataImg['products'][0]['images'][$h]);
                        // dd(Storage::get($data));
                        // dd($attr);
                    }
                } catch (BadResponseException $th) {
                    $texto = "Fallo [" . date("Y-m-d H:i:s") . "] Clave CT [" .$producto[$i]['clave_ct']. "]";
                    Storage::append("BarcodeLookUp-Fallos.txt", $texto);
                    // $faltantes += $faltantes."_".$busqueda;
                    // dd($faltantes);
                }
            }
        }
        dd($producto);
        // $sku = $producto['numParte'];
        // $url = "https://images.barcodelookup.com/56172/561726884-1.jpg";
        // $contents = file_get_contents($url);
        // $datos = pathinfo($url);
        // $nombre = $sku.".".$datos['extension'];
        // $ruta = 'productos/'.$nombre;
        // Storage::put($ruta, $contents);
        // dd($busqueda);
        // $nombre = $sku.".".$datos['extension'];
        // dd($busqueda);

        // LEER JSON LOCAL
        $dataImg = json_decode(file_get_contents(storage_path() . "/app/public/productos-json/ACCACT110.json"), true);
        $valor = $dataImg['products'][0]['barcode_formats'];
        $stringSeparado = explode(",",$valor);
        for($j=0;$j<count($stringSeparado);$j++){
            $pos=strpos($stringSeparado[$j],"UPC-A");
            if(is_int($pos)){
                $datos['upc']=str_replace(" ","",str_replace("UPC-A","",$stringSeparado[$j]));
            }else{
                $datos['upc'] = NULL;
                $pos=strpos($stringSeparado[$j],"EAN-13");
                if($pos>=0){
                    $datos['ean']=str_replace(" ","",str_replace("EAN-13","",$stringSeparado[$j]));
                }
            }
        }
        dd("Acabaste los 5 archivos");

        // LEER DESDE API
        $client = new Client();
        $headers = ['Content-Type' => 'application/json'];
        $url = "https://api.barcodelookup.com/v3/products?barcode=".$busqueda."&key=iw56g6qzhmcws5ogog6b70gktb93fb";

        try {
            //code...
            $res = $client->request('GET', $url, ['headers' => [
                'Accept' => 'application/json',
                'http_errors' => false,
                'Content-Type' => 'application/json',
                'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36"
            ]]);
            // dd($res->getStatusCode());
            $result = $res->getBody();
            $dataImg = json_decode($result, true);
            $nombreJSON = $producto['clave'].".json";
            $newJsonString = json_encode($dataImg, JSON_PRETTY_PRINT);
            Storage::disk('json')->put($nombreJSON, $newJsonString);

            // $valor = "UPC-A 731304206828, EAN-13 0731304206828";
            $valor = $dataImg['products'][0]['barcode_formats'];
            $stringSeparado = explode(",",$valor);
            for($j=0;$j<count($stringSeparado);$j++){
                $pos=strpos($stringSeparado[$j],"UPC-A");
                if(is_int($pos)){
                    $datos['upc']=str_replace(" ","",str_replace("UPC-A","",$stringSeparado[$j]));
                }else{
                    $pos=strpos($stringSeparado[$j],"EAN-13");
                    if($pos>=0){
                        $datos['ean']=str_replace(" ","",str_replace("EAN-13","",$stringSeparado[$j]));
                    }
                }
            }
            $producto = Producto::updateOrCreate(
                ['clave_ct'=>$producto['clave']],
                [
                    'ean'=>$datos['ean'],
                    'upc'=>$datos['upc'],
                    'google_cat'=>$dataImg['products'][0]['category'],
                    'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
                ]
            );
        } catch (BadResponseException $th) {
            //throw $th;
            dd($th);
        }
        // $res = $client->request('GET', $url, ['headers' => [
        //     'Accept' => 'application/json',
        //     'http_errors' => false,
        //     'Content-Type' => 'application/json',
        //     'User-Agent' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36"
        // ]]);
        // dd($res->getStatusCode());
        // $result = $res->getBody();
        // $dataImg = json_decode($result, true);
        // dd($dataImg['products'][0]['barcode_formats']);

        // $valor = "UPC-A 731304206828, EAN-13 0731304206828";
        $valor = $dataImg['products'][0]['barcode_formats'];
        $stringSeparado = explode(",",$valor);
        for($j=0;$j<count($stringSeparado);$j++){
            $pos=strpos($stringSeparado[$j],"UPC-A");
            if(is_int($pos)){
                $datos['upc']=str_replace(" ","",str_replace("UPC-A","",$stringSeparado[$j]));
            }else{
                $pos=strpos($stringSeparado[$j],"EAN-13");
                if($pos>=0){
                    $datos['ean']=str_replace(" ","",str_replace("EAN-13","",$stringSeparado[$j]));
                }
            }
        }
        // dd($datos);

        for($i=0;$i<sizeof($dataImg['products'][0]['images']);$i++){
            $urlImagen = $dataImg['products'][0]['images'][$i];
            $contents = file_get_contents($urlImagen);
            $datos = pathinfo($urlImagen);
            $nombre = $producto['clave']."-".$i.".".$datos['extension'];
            $ruta = 'productos/'.$nombre;
            // $urlImagen->move(public_path('/',$nombre));
            Storage::disk('public')->put($ruta, $contents);
            Storage::put($ruta, $contents);
            $data=$nombre;

            list($width, $height, $type, $attr) = getimagesize($dataImg['products'][0]['images'][$i]);
            // dd(Storage::get($data));
            // dd($attr);
        }
        return view('productos.pruebaImagen', compact('data'));
        // if($data['products'][0]['category']){
        //     dd($data['products'][0]['category']);
        //     $producto = Producto::updateOrCreate(
        //         ['clave_ct'=>$clave_ct],
        //         [
        //             'marca_id'=>$productos[$i]['idMarca'],
        //             'subcategoria_id'=>$productos[$i]['idSubCategoria'],
        //             'categoria_id'=>$productos[$i]['idCategoria'],
        //             'nombre'=>$productos[$i]['nombre'],
        //             'descripcion_corta'=>$productos[$i]['descripcion_corta'],
        //             'precio_unitario'=>$productos[$i]['moneda'] == "USD" ? number_format((($productos[$i]['precio']*$productos[$i]['tipoCambio'])*1.16),2,'.',''):number_format(($productos[$i]['precio']*1.16),2,'.',''),
        //             'sku'=>ltrim($productos[$i]['numParte']),
        //             'ean'=>$productos[$i]['ean'],
        //             'upc'=>$productos[$i]['upc'],
        //             'imagen'=>$productos[$i]['imagen'],
        //             'existencias'=>$existencia_producto,
        //             'estatus'=>$productos[$i]['activo']==1 ? 'Activo':'Descontinuado'
        //         ]
        //     );
        // }
        dd(sizeof($data['products'][0]['images']));

        // for($i=0;$i<sizeof($data['products'][0]['images']);$i++){
        //     $urlImagen = $data['products'][0]['images'][$i];
        //     $contents = file_get_contents($urlImagen);
        //     $datos = pathinfo($urlImagen);
        //     $nombre = $sku."-".$i.".".$datos['extension'];
        //     $ruta = 'productos/'.$nombre;
        //     Storage::put($ruta, $contents);
        // }
        // dd(sizeof($data['products'][0]['images']));

        $remove = array(" ","  ","   ","    ", "(", ")", "$", "*", "/",",","IVA","Incluido");
        $client = new Client();
        for($i=0;$i<1;$i++){
            // $sku = $productos[$i]->sku;
            // $clave_ct = $productos[$i]->clave_ct;
            // if($sku==""){
            //     $sku="NOEXISTE";
            // }
            $website = $client->request('GET', 'https://api.barcodelookup.com/v3/products?barcode=4710886162469&key=u30gi6v08sqi3qw0gstd7ovk3fqfrb');
            // $website = $client->request('GET', 'https://www.zegucom.com.mx/?cons='.$sku.'&mod=search&reg=1');
            $result = $website->filter('body')->first();
            dd($result->text());
            $imagenes[$i] = $result->count() ? $website->filter('.col-50 > #largeProductImage')->first()->text() : $imagenes[$i] = 0;
            dd($imagenes[$i]);
            // $imagenes[$i] = $result->count() ? str_replace($remove, "", $website->filter('.price-text > .result-price-search')->first()->text()) : $imagenes[$i] = 0;
            // $productoZegucom = Zegucom::updateOrCreate(
            //     ['sku'=>$sku, 'clave_ct'=>$clave_ct],
            //     ['precio_unitario'=>$imagenes[$i]]
            // );
        }
        // dd($imagenes);
        return $imagenes;
    }

    public function getFile($filename){
        $file=Storage::disk('productos')->get($filename);
        return (new Response($file, 200))
            ->header('Content-Type', 'image/jpeg');
    }

    public function ejemploImagenes(){
        set_time_limit(0);
        // LEER JSON LOCAL
        $dataImg = json_decode(file_get_contents(storage_path() . "/app/public/productos-json/ACCACT110.json"), true);
        for($i=0;$i<sizeof($dataImg['products'][0]['images']);$i++){
            $urlImagen = $dataImg['products'][0]['images'][$i];
            $contents = file_get_contents($urlImagen);
            $datos = pathinfo($urlImagen);
            $nombre = "ACCACT110-".$i.".".$datos['extension'];
            $ruta = 'productos/'.$nombre;
            Storage::disk('public')->put($ruta, $contents);
            Storage::put($ruta, $contents);
            $data=$nombre;
            list($width, $height, $type, $attr) = getimagesize($dataImg['products'][0]['images'][$i]);
        }
        return view('productos.pruebaImagen', compact('data'));
    }
}
