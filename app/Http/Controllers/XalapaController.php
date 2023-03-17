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

class XalapaController extends Controller
{
    public function ofertasnuevas(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['promociones'] = DB::select("SELECT productos.clave_ct, productos.sku, categorias.nombre AS categoria, subcategorias.nombre AS subcategoria, promociones.descuento, promociones.fecha_fin, promociones.updated_at FROM promociones INNER JOIN productos ON promociones.clave_ct = productos.clave_ct INNER JOIN categorias ON productos.categoria_id = categorias.id INNER JOIN subcategorias ON productos.subcategoria_id = subcategorias.id INNER JOIN existencias ON productos.clave_ct = existencias.clave_ct WHERE productos.estatus = 'Activo' AND existencias.almacen_id = 15 AND existencias.existencias > 4 AND (promociones.consulta = NULL OR promociones.consulta = '".$fecha."') AND EXTRACT(DAY FROM promociones.updated_at)= '".date('d')."';");
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - Nuevas Ofertas (".$fechaR.")";
        Promocion::whereNull('consulta')->update([
            'consulta' => $fecha
        ]);
        Promocion::whereYear('created_at','<', date('Y'))->delete();
        Promocion::whereMonth('updated_at','<',date('m'))->whereYear('created_at', date('Y'))->delete();
        return view('promociones.vigentes', compact('data'));
    }
    
    public function vigentesxalapa(){
        $fecha = date('Y')."-".date('m')."-".date('d');
        $data['promociones'] = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->join('categorias','categorias.id','=','productos.categoria_id')
            ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
            ->join('existencias','existencias.clave_ct','=','productos.clave_ct')
            ->where('productos.estatus','Activo')
            ->where('existencias.almacen_id','=', 15)
            ->where('existencias.existencias','>=', 5)
            ->whereDate('promociones.fecha_fin','>=',$fecha)
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

    public function delmesxalapa(){
        //dd(date('Y'));
        $data['promociones'] = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
            ->join('categorias','categorias.id','=','productos.categoria_id')
            ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
            ->join('existencias','existencias.clave_ct','=','productos.clave_ct')
            ->where('productos.estatus','Activo')
            ->where('existencias.almacen_id','=', 15)
            ->where('existencias.existencias','>=', 5)
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

    public function vencidasxalapa(){
        $fecha = date('Y')."-".date('m')."-".date('d')-1;
        $data['promociones'] = Promocion::join('productos','productos.clave_ct','=','promociones.clave_ct')
        ->join('categorias','categorias.id','=','productos.categoria_id')
        ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
        ->join('existencias','existencias.clave_ct','=','productos.clave_ct')
        ->where('existencias.almacen_id','=', 15)
        ->where('existencias.existencias','>=', 5)
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

    
}
