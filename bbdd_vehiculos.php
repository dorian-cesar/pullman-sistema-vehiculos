<?php

set_time_limit(3000);
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

// Establecer conexión a la base de datos
$servername = "ls-3c0c538286def4da7f8273aa5531e0b6eee0990c.cylsiewx0zgx.us-east-1.rds.amazonaws.com"; // Cambiar si es necesario
$username = "dbmasteruser"; // Cambiar si es necesario
$password = "eF5D;6VzP$^7qDryBzDd,`+w(5e4*qI+"; // Cambiar si es necesario
$dbname = "masgps";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

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
    
    // Insertar los datos en la tabla infoVehiculos
    $sql = "INSERT INTO infoVehiculos (patente, nroInterno, centroCosto, marcaChasis, modeloChasis, marcaCarroceria, modeloCarroceria, ano, unidadNegocio, estado, flota, ubicacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $patente, $nroInterno, $centroCosto, $marcaChasis, $modeloChasis, $marcaCarroceria, $modeloCarroceria, $ano, $unidadNegocio, $estado, $flota, $ubicacion);
    
    if ($stmt->execute()) {
        echo "Registro insertado correctamente.<br>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>
