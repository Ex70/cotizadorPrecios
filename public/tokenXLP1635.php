<?php
try{
    ini_set('display_errors', 1);

    function getIp(){
        $datosIp = json_decode(file_get_contents('https://api.myip.com/'));
        echo "<pre>";
        echo 'IP: '.$datosIp->ip;
        echo "<br><br>";
    }

    function servicioApi($metodo, $servicio, $json = null, $token = null) {

        $ch = curl_init('http://connect.ctonline.mx:3001/' . $servicio);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($json != null)
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json), 'x-auth: ' . $token));
        else{
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',  'x-auth: ' . $token));
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        $result = curl_exec($ch);       

        if ($result === false || curl_error($ch)) throw new Exception("Err. CURL => ".curl_error($ch));    

        curl_close($ch);            
        return json_decode($result);
    }

    function crearNuevoToken() {
        //Credenciales del cliente para poder consumir los servicios

        $cliente = 'XLP1635';
        $email = 'ehscompras@hotmail.com';
        $rfc = 'EHS150529ME8'; 
   
      

        $servicio = 'cliente/token'; //Ruta del servicio para la creacion de un nuevo token
        $json = json_encode(array('email' => $email, 'cliente' => $cliente, 'rfc' => $rfc));

        //AQUI SE CONSUME UN SERVICIO POR == METODO POST == y SE RETORNA COMO RESPUESTA
        return servicioApi('POST', $servicio, $json, null);
    }

    getIp();
      
    $codigoArticulo = 'MONDLL3120'; //Codigo del articulo para formar la ruta del servicio
    $almacen = '01A'; //Almacen para formar la ruta del servicio
    $servicio = 'existencia/promociones/'.$codigoArticulo.""; //Ruta del servicio para consultar existencia de articulo por almancen
    $vres = servicioApi('GET', $servicio, null, crearNuevoToken()->token);

    //AQUI SE IMPRIME EN PANTALLA (NAVEGADOR WEB - CHROME) LOS RESULTADOS    
    // print_r($datos);
    // print_r($vres);

    $tipoCambio = 19.80;    
    $precioCT = ($vres->moneda == "USD")? $vres->precio * $tipoCambio : $vres->precio;    

    if ($precioCT<=1000)        $precioCT += 100;
    else if ($precioCT<=1500)   $precioCT += 150;
    else if ($precioCT>1500)    $precioCT *= 1.12;

    echo "*** precioCT. $precioCT";
}catch(Exception $e){
    echo "<pre>";
    echo $e->getMessage();    
}
?>
