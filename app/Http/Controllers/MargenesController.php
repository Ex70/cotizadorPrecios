<?php

namespace App\Http\Controllers;

use App\Models\Margenes;
use App\Models\Producto;
use Google\Service\FirebaseManagement\AndroidApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $suma = 0.0; $sumPromedio = 0.0; $sumaCT = 0.0; $a =1; $margenesTotales=0;
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

    public function cartaMargenes1(){
        $data['productos'] = DB::select("SELECT productos.clave_ct, productos.nombre, categorias.nombre AS categoria, subcategorias.nombre AS subcategoria, marcas.nombre AS marca, productos.enlace, productos.imagen, productos.existencias, margenes.margen_utilidad AS margen FROM productos INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategorias.id INNER JOIN marcas ON productos.marca_id = marcas.id INNER JOIN margenes ON (productos.categoria_id = margenes.categoria_id AND productos.subcategoria_id = margenes.subcategoria_id AND productos.marca_id = margenes.marca_id) WHERE productos.estatus = 'Activo' AND margenes.margen_utilidad > 0.1 AND productos.existencias > 0;");
            return view('margenes.cartaMargenes', compact('data'));
        }

        public function cartaMargenes2(){
            $data['productos'] = DB::select("SELECT productos.clave_ct, productos.nombre, categorias.nombre AS categoria, subcategorias.nombre AS subcategoria, marcas.nombre AS marca, productos.enlace, productos.imagen, productos.existencias, margenes.margen_utilidad AS margen FROM productos INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategorias.id INNER JOIN marcas ON productos.marca_id = marcas.id INNER JOIN margenes ON (productos.categoria_id = margenes.categoria_id AND productos.subcategoria_id = margenes.subcategoria_id AND productos.marca_id = margenes.marca_id) WHERE productos.estatus = 'Activo' AND margenes.margen_utilidad > 0 AND margenes.margen_utilidad  <= 0.1 AND productos.existencias > 0;");
                return view('margenes.cartaMargenes', compact('data'));
            }
    }
