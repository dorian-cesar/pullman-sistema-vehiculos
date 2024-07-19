<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.pullman.cl/srv-vehiculo-core-web/rest/informes/obtenerInformeVehiculosExcel',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;

$vehiculos_data = json_decode($response, true);
$vehiculos = array_slice($vehiculos_data['response'], 2);

$i=0;

foreach ($vehiculos as $v) {
    $patente = (isset($v['prop01']) ? $v['prop01'] : "");
    $nroInterno = (isset($v['prop02']) ? $v['prop02'] : "");
    $centroCosto = (isset($v['prop19']) ? $v['prop19'] : "");
    $marcaChasis = (isset($v['prop05']) ? $v['prop05'] : "");
    $modeloChasis = (isset($v['prop06']) ? $v['prop06'] : "");
    $marcaCarroceria = (isset($v['prop07']) ? $v['prop07'] : "");
    $modeloCarroceria = (isset($v['prop08']) ? $v['prop08'] : "");
    $ano = (isset($v['prop09']) ? $v['prop09'] : "");
    $unidadNegocio = (isset($v['prop17']) ? $v['prop17'] : "");
    $estado = (isset($v['prop13']) ? $v['prop13'] : "");
    $flota = (isset($v['prop21']) ? $v['prop21'] : "");
    $ubicacion = (isset($v['prop18']) ? $v['prop18'] : "");
   
    
    $data=array(
        'patente'=>$patente,
        'nroInterno'=>$nroInterno,
        'centroCosto'=>$centroCosto,
        'marcaChasis'=>$marcaChasis,
        'modeloChasis'=>$modeloChasis,
        'marcaCarroceria'=>$marcaCarroceria,
        'modeloCarroceria'=>$modeloCarroceria,
        'ano'=>$ano,
        'unidadNegocio'=>$unidadNegocio,
        'estado'=>$estado,
        'flota'=>$flota,
        'ubicacion'=>$ubicacion



    );

    $total[$i] = $data;
    $i++;

}

echo json_encode($total);
