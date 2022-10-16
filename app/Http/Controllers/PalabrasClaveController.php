<?php

namespace App\Http\Controllers;

use App\Models\Palabras;
use Illuminate\Http\Request;

class PalabrasClaveController extends Controller{
    public function nuevas(){
        $palabrasClave = Palabras::whereDay('created_at', date('d')-1)->get();
        dd($palabrasClave);
    }
}
