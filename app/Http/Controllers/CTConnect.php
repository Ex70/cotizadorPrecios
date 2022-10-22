<?php

namespace App\Http\Controllers;

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
        $url = "http://connect.ctonline.mx:3001/cliente/token";
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

    public function existencias($productos){
        set_time_limit(0);
        $token = Token::all()->last()->token;
        $headers = [
            'x-auth' => $token,
            'Accept'        => 'application/json',
        ];
        $client = new Client();
        for($i=0;$i<sizeof($productos);$i++){
            // dd($productos[$i]['clave']);
            $clave_ct = $productos[$i]->clave_ct;
            $sku = $productos[$i]->sku;
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
}
