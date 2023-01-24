<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class jsonLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json:local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cargar JSON Local';

    /** 
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //return 0;
        
        //$this->call('\App\Http\Controllers\PreciosController@lecturaLocal');
        //$productos = storage_path() . "/app/public/productos.json";
        $productos = Storage::get('public/products.json');
        $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "] Tamaño: " .filesize($productos);
        Storage::disk('local')->append("RegistroJson.txt", $texto);
        Storage::append("RegistroJson.txt", $texto);
    }
}
