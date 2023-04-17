<?php

namespace App\Http\Controllers;

use App\Models\MargenesProducto;
use App\Models\Producto;
use App\Models\Token;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class CTConnect extends Controller
{
    public function token(){
        set_time_limit(0);
        $client = new Client();
        // $url = "http://connect.ctonline.mx:3001/cliente/token";
        // dd($url);
        $res = $client->request('POST', 'http://connect.ctonline.mx:3001/cliente/token', [
            'form_params' => [
                'email' => 'ehscompras@hotmail.com',
                'cliente' => 'XLP1635',
                'rfc' => 'EHS150529ME8'
            ]
        ]);
        $result = $res->getBody();
        $data = json_decode($result, true);
            $token = Token::updateOrCreate(
                ['token' => $data['token']]
            );
        return $token;
    }

    public function existencias($productos=0){
        set_time_limit(0);
        $this->token();
        $token = Token::all()->last()->token;
        $headers = [
            'x-auth' => $token,
            'Accept'        => 'application/json',
        ];
        $client = new Client();
        if($productos==0){
            $productos = json_decode(file_get_contents(storage_path() . "/app/public/productos.json"), true);
        }
        for($i=0;$i<sizeof($productos);$i++){
            // $clave_ct = $productos[$i]->clave_ct;
            $clave_ct= $productos[$i]['clave'];
            $sku= $productos[$i]['numParte'];
            // $sku = $productos[$i]->sku;
            $url = "http://connect.ctonline.mx:3001/existencia/".$clave_ct."/TOTAL";
            try {
                $res = $client->request('GET', $url, [
                    'headers' => $headers
                ]);
                $result = $res->getBody();
                $data = json_decode($result, true);
                $existencias[$i]= $data['existencia_total'];
                Producto::updateOrCreate(
                    ['sku'=>$sku, 'clave_ct'=>$clave_ct],
                    ['existencias'=>$data['existencia_total']]
                );
            }catch (ClientException $e) {
                $i = $i--;
                $this->token();
                $token = Token::all()->last()->token;
                $headers = [
                    'x-auth' => $token,
                    'Accept'        => 'application/json',
                ];
            }
        }
    }

    public function existenciaProductoWP($clave_ct){
        set_time_limit(0);
        $this->token();
        $token = Token::all()->last()->token;
        $headers = [
            'x-auth' => $token,
            'Accept'        => 'application/json',
        ];
        $client = new Client();
        $url = "http://connect.ctonline.mx:3001/existencia/".$clave_ct."/TOTAL";
        try {
            $res = $client->request('GET', $url, [
                'headers' => $headers
            ]);
            $result = $res->getBody();
            $data = json_decode($result, true);
            $existencia= $data['existencia_total'];
            Producto::updateOrCreate(
                ['clave_ct'=>$clave_ct],
                ['existencias'=>$existencia]
            );
            return $existencia;
        }catch (ClientException $e) {
            $this->token();
            $token = Token::all()->last()->token;
            $headers = [
                'x-auth' => $token,
                'Accept'        => 'application/json',
            ];
        }
    }

    public function preciosProductoWP($clave_ct){
        set_time_limit(0);
        $this->token();
        $token = Token::all()->last()->token;
        $headers = [
            'x-auth' => $token,
            'Accept'        => 'application/json',
        ];
        $client = new Client();
        $url = "http://connect.ctonline.mx:3001/existencia/promociones/".$clave_ct."";
        try {
            $res = $client->request('GET', $url, [
                'headers' => $headers
            ]);
            $result = $res->getBody();
            $data = json_decode($result, true);
            // dd($data);
            $margen=MargenesProducto::select('margen_utilidad')->where('clave_ct',$clave_ct)->get();
            $data['precio'] = $data['moneda'] == "USD" ? number_format(($data['precio'] * $this->divisa()*1.16), 2, '.', '') : number_format($data['precio']*1.16,2,'.','');
            // dd($margen[0]['margen_utilidad']);
            // number_format((($productos[$i]['precio'] * $productos[$i]['tipoCambio']) * 1.16), 2, '.', '')
            $precios['normal']= number_format($data['precio']*(1+$margen[0]['margen_utilidad']),2,'.','');
            $precios['rebajado']= isset($data['almacenes'][0]['promocion']) ? isset($data['almacenes'][0]['promocion']['precio']) ? $data['moneda'] == "USD" ? number_format($data['almacenes'][0]['promocion']['precio']*$this->divisa()*1.16*(1+$margen[0]['margen_utilidad']),2,'.','') :  number_format($data['almacenes'][0]['promocion']['precio']*1.16*(1+$margen[0]['margen_utilidad']),2,'.','') : number_format($data['precio']*($data['almacenes'][0]['promocion']['porciento']/100)*(1+$margen[0]['margen_utilidad']),2,'.','') : '';
            // dd($precios);
            // Producto::updateOrCreate(
            //     ['clave_ct'=>$clave_ct],
            //     ['existencias'=>$existencia]
            // );
            return $precios;
        }catch (ClientException $e) {
            $this->token();
            $token = Token::all()->last()->token;
            $headers = [
                'x-auth' => $token,
                'Accept'        => 'application/json',
            ];
        }
    }

    public function divisa(){
        set_time_limit(0);
        $this->token();
        $token = Token::all()->last()->token;
        $headers = [
            'x-auth' => $token,
            'Accept'        => 'application/json',
        ];
        $client = new Client();
        $url = "http://connect.ctonline.mx:3001/pedido/tipoCambio";
        try {
            $res = $client->request('GET', $url, [
                'headers' => $headers
            ]);
            $result = $res->getBody();
            $data = json_decode($result, true);
            // dd($data);
            // $existencia= $data['existencia_total'];
            return $data['tipoCambio'];
        }catch (ClientException $e) {
            $this->token();
            $token = Token::all()->last()->token;
            $headers = [
                'x-auth' => $token,
                'Accept'        => 'application/json',
            ];
        }
    }
}
