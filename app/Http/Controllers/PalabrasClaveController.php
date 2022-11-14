<?php

namespace App\Http\Controllers;

use App\Models\Palabras;
use Illuminate\Http\Request;

class PalabrasClaveController extends Controller{
    public function nuevas(){
        $palabrasClave = Palabras::whereDay('created_at', date('d'))->whereMonth('created_at', date('m'))->get();
        // dd($palabrasClave);
        return view('palabras-clave.nuevas', compact('palabrasClave'));
    }
}
