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
  CURLOPT_POSTFIELDS => '{}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);

$vehiculos_data = json_decode($response, true);
$vehiculos = array_slice($vehiculos_data['response'], 2);

// Establecer conexión a la base de datos
$servername = "ls-3c0c538286def4da7f8273aa5531e0b6eee0990c.cylsiewx0zgx.us-east-1.rds.amazonaws.com";
$username = "dbmasteruser";
$password = "eF5D;6VzP$^7qDryBzDd,`+w(5e4*qI+";
$dbname = "masgps";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}

// Truncar la tabla infoVehiculos
$sql_truncate = "TRUNCATE TABLE infoVehiculos";
if ($conn->query($sql_truncate) === TRUE) {
  echo "Tabla infoVehiculos truncada correctamente.<br>";
} else {
  echo "Error truncando la tabla: " . $conn->error . "<br>";
}

// Dividir los registros en lotes de 50
$batch_size = 50;
$vehiculos_batches = array_chunk($vehiculos, $batch_size);

foreach ($vehiculos_batches as $batch) {
    $multi_curl = curl_multi_init();
    $handles = [];

    foreach ($batch as $v) {
        $ch = curl_init();
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
        $descCentroCosto = (isset($v['prop20']) ? $v['prop20'] : "");
        $descFlota = (isset($v['prop22']) ? $v['prop22'] : "");

       // if (strpos($estado, 'ACTIVO') === 0) {
            // Crear la consulta de inserción
            $sql = "INSERT INTO infoVehiculos (patente, nroInterno, centroCosto, marcaChasis, modeloChasis, marcaCarroceria, modeloCarroceria, ano, unidadNegocio, estado, flota, ubicacion, descCentroCosto,descFlota) VALUES ('$patente', '$nroInterno', '$centroCosto', '$marcaChasis', '$modeloChasis', '$marcaCarroceria', '$modeloCarroceria', '$ano', '$unidadNegocio', '$estado', '$flota', '$ubicacion', '$descCentroCosto','$descFlota')";
            
            curl_setopt($ch, CURLOPT_URL, 'http://3.81.103.141/pullman-sistema-vehiculos/insertion-endpoint.php'); // Debes crear un endpoint para manejar la inserción en la misma máquina.
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ['query' => $sql]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($multi_curl, $ch);
            $handles[] = $ch; 
      //  }
    }

    // Ejecutar todas las solicitudes en paralelo
    do {
        curl_multi_exec($multi_curl, $running);
        curl_multi_select($multi_curl);
    } while ($running > 0);

    // Cerrar los manejadores
    foreach ($handles as $ch) {
        curl_multi_remove_handle($multi_curl, $ch);
        curl_close($ch);
    }

    curl_multi_close($multi_curl);
}

$conn->close();
?>
