<?php

namespace App\Http\Controllers;

use App\Models\Palabras;
use Illuminate\Http\Request;

class PalabrasClaveController extends Controller{
    public function nuevas(){
        $data['palabrasClave'] = Palabras::whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->get();
        // dd($palabrasClave);
        $fechaR = date('d')."-".date('m')."-".date('Y');
        $data['titulo'] = "EHS - Palabras Nuevas (".$fechaR.")";
        return view('palabras-clave.nuevas', compact('data'));
    }
}
