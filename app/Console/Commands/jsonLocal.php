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
        $fileExist = Storage::disk('prueba-ftp')->exists('catalogo_xml/productos.json');
        if ($fileExist){;
            //Storage::disk('local')->put('public/products.json',Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
            Storage::disk('local')->put('public/products.json',Storage::disk('prueba-ftp')->get('catalogo_xml/productos.json'));
        } else{
            dd("No existe");
        }
        $docSize = storage_path("app/public/products.json");
        switch (filesize($docSize)) {
            case 655360:
                // sleep(600);
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error 655360";;
                break;
            case 1310720:
                // sleep(540);
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error 1310720";
            case 1966080:
                // sleep(480);
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error 1966080";
                break;
            case 2621440:
                // sleep(420);
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error 2621440";
                break;
            case 3276800:
                // sleep(360);
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error 3276800";
                break;
            case 3637248:
                // sleep(300);
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error 3637248";
                break;
            case 3932160:
                // sleep(240);
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error 3932160";
                break;
            case 4587520:
                // sleep(180);
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "], Error 4587520";
                break;
            default:
                // $this->call('\App\Http\Controllers\PreciosController@lecturaPrueba');
                Storage::disk('local')->put("public/products-". filesize($docSize) .".json", Storage::get('public/products.json'));
                $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "] Tamaño [" .filesize($docSize) . "] Sin Errores";
                break;
        }
        Storage::append("RegistroJson.txt", $texto);
    }
}
