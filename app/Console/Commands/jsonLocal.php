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
        
        $this->call('\App\Http\Controllers\PreciosController@lecturaLocal');
        $texto = "Archivo Cargado [" . date("Y-m-d H:i:s") . "]";
        Storage::append("RegistroJson.txt", $texto);
    }
}
