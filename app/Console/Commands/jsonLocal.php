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
        //dd("hola");
        $fileExist = Storage::disk('prueba-ftp')->exists('catalogo_xml/productos.json');
        if ($fileExist) {
            $texto = "Archivo Existe [" . date("Y-m-d H:i:s") . "]";
            Storage::append("RegistroJson.txt", $texto);
            $size = Storage::disk('prueba-ftp')->get("catalogo_xml/productos.json");
            $texto2 = "Archivo Existe [". filesize($size) ."]";
            Storage::append("RegistroJson.txt", $texto2);
            // $fsize = filesize($size);
            // $texto2 = "El Tamaño del archivo es:" . $fsize;
            // Storage::append("RegistroJson.txt", $texto2);
            //Storage::disk('local')->put('public/products.json',Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
            //Storage::disk('local')->put('public/products-' . filesize($size) . '.json', Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
        } else {
            dd("No existe");
        }
        $size = filesize(storage_path('catalogo_xml/productos.json'));
        // switch ($size) {
        //     case 655360:
        //         sleep(600);
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 1 Tamaño: " . $size;
        //         break;
        //     case 1310720:
        //         sleep(540);
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 2 Tamaño: " . $size;
        //         break;
        //     case 1966080:
        //         sleep(480);
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 3 Tamaño: " . $size;
        //         break;
        //     case 2621440:
        //         sleep(420);
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 4 Tamaño: " . $size;
        //         break;
        //     case 3276800:
        //         sleep(360);
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 5 Tamaño: " . $size;
        //         break;
        //     case 3363724832160:
        //         sleep(300);
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 6 Tamaño: " . $size;
        //         break;
        //     case 3932160:
        //         sleep(240);
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 7 Tamaño: " . $size;
        //         break;
        //     case 4587520:
        //         sleep(180);
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 8 Tamaño: " . $size;
        //         break;
        //     default:
        //         $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
        //         $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 0 Tamaño: " . $size;
        //         break;
        // }
        $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error: 0 Tamaño: " .$size;
        Storage::append("RegistroJson.txt", $texto);
    }
}
