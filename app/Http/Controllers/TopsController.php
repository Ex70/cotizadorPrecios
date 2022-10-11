<?php

namespace App\Http\Controllers;

use App\Imports\TopsImport;
use App\Models\Producto;
use App\Models\Top;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac;

class TopsController extends Controller{
    public function index(Request $request){
        if(empty($request->month)){
            $month = date('m');
        }else{
            $month = $request->month;
        }
        if(empty($$request->year)){
            $year = date('Y');
        }else{
            $year = $request->year;
        }
        $productos['productos'] = Producto::join('categorias','categorias.id','=','productos.categoria_id')
                            ->join('subcategorias','subcategorias.id','=','productos.subcategoria_id')
                            ->join('marcas','marcas.id','=','productos.marca_id')
                            ->join('tops_mensuales','tops_mensuales.clave_ct','=','productos.clave_ct')
                            ->where('tops_mensuales.mes','=', $month)
                            ->where('tops_mensuales.anio','=', $year)
                            ->get([
                                'productos.nombre',
                                'productos.clave_ct',
                                'productos.sku',
                                'productos.categoria_id',
                                'productos.subcategoria_id',
                                'productos.marca_id',
                                'productos.descripcion_corta',
                                'productos.enlace',
                                'productos.imagen',
                                'categorias.nombre as categoria',
                                'subcategorias.nombre as subcategoria',
                                'marcas.nombre as marca'
                            ]);
        switch(str_replace('0','',$month)){
            case 1: $productos['mes'] = "Enero"; break;
            case 2: $productos['mes'] = "Febrero"; break;
            case 3: $productos['mes'] = "Marzo"; break;
            case 4: $productos['mes'] = "Abril"; break;
            case 5: $productos['mes'] = "Mayo"; break;
            case 6: $productos['mes'] = "Junio"; break;
            case 7: $productos['mes'] = "Julio"; break;
            case 8: $productos['mes'] = "Agosto"; break;
            case 9: $productos['mes'] = "Septiembre"; break;
            case 10: $productos['mes'] = "Octubre"; break;
            case 11: $productos['mes'] = "Noviembre"; break;
            case 12: $productos['mes'] = "Diciembre"; break;
        }
        return view('top100.table-datatable', compact('productos'));
    }

    public function importTops(){
        Excel::import(new TopsImport, request()->file('file'));
        return redirect()->back()->with('message', 'Archivo subido con éxito');
    }
}
