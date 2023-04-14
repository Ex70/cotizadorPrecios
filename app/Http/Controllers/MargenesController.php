<?php

namespace App\Http\Controllers;

use App\Models\Margenes;
use App\Models\MargenesProducto;
use App\Models\Producto;
use Google\Service\FirebaseManagement\AndroidApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


class MargenesController extends Controller
{
    public function index(){
        // $sql = "SELECT * FROM vw_vistaCombinaciones";
        $sql = DB::table('vw_vistaCombinaciones')->get();
        $data['combinaciones'] = $sql;
        // dd($sql);
        foreach ($sql as $key => $combinacion) {
            $data['margenes'][$key] = $this->margenes($combinacion->categoria_id,$combinacion->subcategoria_id,$combinacion->marca_id,$combinacion->existencias);
            // $data['margenes'][$key] = $this->margenes(860,862,236,17186);
            // dd($data['margenes'][$key]);
        }
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - Margenes de Utilidad (".$fechaR.")";
        return view('margenes.index', compact('data'));
    }

    public function margenes($categoria,$subcategoria,$marca,$existencias){
        $suma = 0.0; $sumPromedio = 0.0; $sumaCT = 0.0; $a =1; $margenesTotales=0; $mediana=0;
        $margenes=[];
        $data['productos'] = Producto::join('categorias','categorias.id','=','productos.categoria_id')
                ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
                ->join('marcas','marcas.id','=','productos.marca_id')
                ->join('abasteo','abasteo.sku','=','productos.sku')
                ->join('mipc','mipc.sku','=','productos.sku')
                ->join('zegucom','zegucom.sku','=','productos.sku')
                ->where('productos.categoria_id',$categoria)
                ->where('productos.subcategoria_id',$subcategoria)
                ->where('productos.marca_id',$marca)
                ->where('productos.existencias','>',0)
                ->where('productos.estatus','Activo')
                ->orderBy('productos.existencias')
                ->get([
                    'productos.existencias',
                    'productos.precio_unitario as precioct',
                    'abasteo.precio_unitario as abasteo',
                    'mipc.precio_unitario as mipc',
                    'zegucom.precio_unitario as zegucom',
                ]);
        foreach ($data['productos'] as $key => $row) {
            $divisor = 3;
            if($row->abasteo < ($row->precioct*.55)||$row->abasteo > ($row->precioct*1.55)){
                $row->abasteo = 0;
            }
            if($row->abasteo == 0){
                $divisor = $divisor-1;
            }
            if($row->mipc < ($row->precioct*.55)||$row->mipc > ($row->precioct*1.55)){
                $row->mipc = 0;
            }
            if($row->mipc == 0){
                $divisor = $divisor-1;
            }
            if($row->zegucom < ($row->precioct*.55)||$row->zegucom > ($row->precioct*1.55)){
                $row->zegucom = 0;
            }
            if($row->zegucom == 0){
                $divisor = $divisor-1;
            }
            $suma = $row->abasteo + $row->cyberpuerta + $row->mipc + $row->zegucom;
            if($divisor>0){
                $promedio = $suma/$divisor;
                $sumPromedio = $sumPromedio + $promedio;
            }else{
                $promedio = 0;
                $sumPromedio = $sumPromedio + $row->precioct;
            }
            if($divisor>0){
                $precioMargenTotal = $row->existencias*number_format((1-($row->precioct/$promedio)),4);
            }else{
                $precioMargenTotal = 0;
            }
            $margenesTotales = $margenesTotales + $precioMargenTotal;
        }
        $margen = Margenes::updateOrCreate(
            ['categoria_id'=>$categoria,'subcategoria_id'=>$subcategoria,'marca_id'=>$marca],
            [
                'existencias'=>$existencias,
                'margen_utilidad'=>number_format(($margenesTotales/$existencias),4)
            ]
        );
        return $margenesTotales/$existencias;
    }

    public function cartaMayor(){
        $data = Producto::Join('margenes', function ($margenes) {
            $margenes->on('productos.categoria_id', '=', 'margenes.categoria_id')
                ->on('productos.subcategoria_id', '=', 'margenes.subcategoria_id')
                ->on('productos.marca_id', '=', 'margenes.marca_id');
            })
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('subcategorias', 'productos.subcategoria_id', '=', 'subcategorias.id')
            ->join('marcas', 'productos.marca_id', '=', 'marcas.id')
            ->where('productos.estatus', 'Activo')
            ->where('productos.existencias', '>', 0)
            ->where('margenes.margen_utilidad', '>', 0.1)
            ->orderBy('margenes.margen_utilidad', 'desc')
            ->get([
                'productos.clave_ct',
                'productos.nombre',
                'categorias.nombre as categoria',
                'subcategorias.nombre as subcategoria',
                'marcas.nombre as marca',
                'productos.enlace',
                'productos.imagen',
                'productos.existencias',
                'margenes.margen_utilidad as margen'
            ])
            ->toArray();
        $data = $this->paginate($data, 20);
        $data->withPath('/margenes/mayor');
        //dd($data);
        //DB::select("SELECT productos.clave_ct, productos.nombre, categorias.nombre AS categoria, subcategorias.nombre AS subcategoria, marcas.nombre AS marca, productos.enlace, productos.imagen, productos.existencias, margenes.margen_utilidad AS margen FROM productos INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategorias.id INNER JOIN marcas ON productos.marca_id = marcas.id INNER JOIN margenes ON (productos.categoria_id = margenes.categoria_id AND productos.subcategoria_id = margenes.subcategoria_id AND productos.marca_id = margenes.marca_id) WHERE productos.estatus = 'Activo' AND margenes.margen_utilidad > 0.1 AND productos.existencias > 0 LIMIT 0,20;"); 
        return view('cartas.cartas', compact('data'));
        }

        public function cartaMenor(){
            $data = Producto::Join('margenes', function ($margenes) {
                $margenes->on('productos.categoria_id', '=', 'margenes.categoria_id')
                    ->on('productos.subcategoria_id', '=', 'margenes.subcategoria_id')
                    ->on('productos.marca_id', '=', 'margenes.marca_id');
                })
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->join('subcategorias', 'productos.subcategoria_id', '=', 'subcategorias.id')
                ->join('marcas', 'productos.marca_id', '=', 'marcas.id')
                ->where('productos.estatus', 'Activo')
                ->where('productos.existencias', '>', 0)
                ->where('margenes.margen_utilidad', '>', 0)
                ->where('margenes.margen_utilidad', '<', 0.1)
                ->orderBy('margenes.margen_utilidad', 'desc')
                ->get([
                    'productos.clave_ct',
                    'productos.nombre',
                    'categorias.nombre as categoria',
                    'subcategorias.nombre as subcategoria',
                    'marcas.nombre as marca',
                    'productos.enlace',
                    'productos.imagen',
                    'productos.existencias',
                    'margenes.margen_utilidad as margen'
                ])
                ->toArray();
            $data = $this->paginate($data, 20);
            $data->withPath('/margenes/menor');
            //$data['productos'] = DB::select("SELECT productos.clave_ct, productos.nombre, categorias.nombre AS categoria, subcategorias.nombre AS subcategoria, marcas.nombre AS marca, productos.enlace, productos.imagen, productos.existencias, margenes.margen_utilidad AS margen FROM productos INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategorias.id INNER JOIN marcas ON productos.marca_id = marcas.id INNER JOIN margenes ON (productos.categoria_id = margenes.categoria_id AND productos.subcategoria_id = margenes.subcategoria_id AND productos.marca_id = margenes.marca_id) WHERE productos.estatus = 'Activo' AND margenes.margen_utilidad > 0 AND margenes.margen_utilidad  <= 0.1 AND productos.existencias > 0 LIMIT 0,21;");
            return view('cartas.cartas', compact('data'));  
        }

    public function paginate($items, $perPage = 20, $page = null){
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage ;
        $itemstoshow = array_slice($items , $offset , $perPage);
        return new LengthAwarePaginator($itemstoshow ,$total ,$perPage);
    }

    public function nuevosmargenes(){
        set_time_limit(0);
    // public function nuevosmargenes($categoria,$subcategoria,$marca,$existencias){
        $suma = 0.0; $sumPromedio = 0.0; $sumaCT = 0.0; $a =1; $margenesTotales=0; $mediana=0;
        $margenes=[];
        $data['productos'] = Producto::join('categorias','categorias.id','=','productos.categoria_id')
                ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
                ->join('marcas','marcas.id','=','productos.marca_id')
                ->join('abasteo','abasteo.sku','=','productos.sku')
                ->join('mipc','mipc.sku','=','productos.sku')
                ->join('zegucom','zegucom.sku','=','productos.sku')
                // ->where('productos.categoria_id',3)
                // ->where('productos.subcategoria_id',97)
                // ->where('productos.marca_id',267)
                // ->where('productos.clave_ct','MEMDAT2380')
                ->where('productos.existencias','>',0)
                ->where('productos.estatus','Activo')
                ->orderBy('productos.existencias')
                ->get([
                    'productos.clave_ct',
                    'productos.precio_unitario as precioct',
                    'abasteo.precio_unitario as abasteo',
                    'mipc.precio_unitario as mipc',
                    'zegucom.precio_unitario as zegucom',
                ])
                ->groupBy('productos.clave_ct');
                dd(count($data['productos']));
        foreach ($data['productos'] as $key => $row) {
            $margenes=[];
            $freq_0 = ($row->abasteo < ($row->precioct*.55)||$row->abasteo > ($row->precioct*1.55)) ? 0 : $row->abasteo;
            // dd($freq_0);
            array_push($margenes,($row->abasteo < ($row->precioct*.55)||$row->abasteo > ($row->precioct*1.55)) ? 0 : $row->abasteo);
            array_push($margenes,($row->mipc < ($row->precioct*.55)||$row->mipc > ($row->precioct*1.55)) ? 0 : $row->mipc);
            array_push($margenes,($row->zegucom < ($row->precioct*.55)||$row->zegucom > ($row->precioct*1.55)) ? 0 : $row->zegucom);
            // array_push($margenes,0);
            // array_push($margenes,0);
            // $margen = ($this->ordenarArreglo($margenes)<0||$this->ordenarArreglo($margenes)>1) ? 0.10 : $this->ordenarArreglo($margenes);
            $margen = $this->ordenarArreglo($margenes);
            $valor = number_format(((($margen/$row->precioct)-1)<0||(($margen/$row->precioct)-1)>0.5) ? 0.10 : (($margen/$row->precioct)-1),4);
            // dd($this->ordenarArreglo($margenes));
            $margen = MargenesProducto::updateOrCreate(
                ['clave_ct'=>$row->clave_ct],
                [
                    'margen_utilidad'=>$valor
                ]
            );
            // dd($valor);
            // dd($data['productos'][$key]['precioct']);
            // dd($data['productos'][$key]);
            // $divisor = 3;
            // if($row->abasteo < ($row->precioct*.55)||$row->abasteo > ($row->precioct*1.55)){
            //     $row->abasteo = 0;
            // }
            // if($row->abasteo == 0){
            //     $divisor = $divisor-1;
            // }
            // if($row->mipc < ($row->precioct*.55)||$row->mipc > ($row->precioct*1.55)){
            //     $row->mipc = 0;
            // }
            // if($row->mipc == 0){
            //     $divisor = $divisor-1;
            // }
            // if($row->zegucom < ($row->precioct*.55)||$row->zegucom > ($row->precioct*1.55)){
            //     $row->zegucom = 0;
            // }
            // if($row->zegucom == 0){
            //     $divisor = $divisor-1;
            // }
            // $suma = $row->abasteo + $row->cyberpuerta + $row->mipc + $row->zegucom;
            // if($divisor>0){
            //     $promedio = $suma/$divisor;
            //     $sumPromedio = $sumPromedio + $promedio;
            // }else{
            //     $promedio = 0;
            //     $sumPromedio = $sumPromedio + $row->precioct;
            // }
            // if($divisor>0){
            //     $precioMargenTotal = $row->existencias*number_format((1-($row->precioct/$promedio)),4);
            // }else{
            //     $precioMargenTotal = 0;
            // }
            // $margenesTotales = $margenesTotales + $precioMargenTotal;
        }
        dd("Listo");
        // $margen = Margenes::updateOrCreate(
        //     ['categoria_id'=>$categoria,'subcategoria_id'=>$subcategoria,'marca_id'=>$marca],
        //     [
        //         'existencias'=>$existencias,
        //         'margen_utilidad'=>number_format(($margenesTotales/$existencias),4)
        //     ]
        // );
        // return $margenesTotales/$existencias;
    }

    function ordenarArreglo($arreglo){
        // get keys of passed array
        $freqs = array_count_values($arreglo);
        $freq_0 = isset($freqs['0']) ? $freqs['0'] : 0;
        $array = array_keys($arreglo);
        $iCount = count($array);
        $middle_index = floor($iCount / 2);
        sort($arreglo, SORT_NUMERIC);
        $median = $arreglo[$middle_index];
        // if ($iCount % 2 == 0 && $freq_0==0) {
        // if ($freq_0==0) {
        //     $median = ($median + $arreglo[$middle_index - 1]) / 2;
        // }else{
            if ($freq_0==2) {
                $median = $arreglo[$middle_index + 1];
            }else{
                if ($freq_0==3) {
                    $median = 0;
                }
            }
        return floatval($median-1);
        // return number_format($median-1,2);
        // return $median;
    }
}