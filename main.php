<?php
// Aumentar el tiempo máximo de ejecución a 300 segundos (5 minutos)
set_time_limit(300);

// Conexión a la base de datos

$servername = "ls-3c0c538286def4da7f8273aa5531e0b6eee0990c.cylsiewx0zgx.us-east-1.rds.amazonaws.com"; // Cambiar si es necesario
$username = "dbmasteruser"; // Cambiar si es necesario
$password = "eF5D;6VzP$^7qDryBzDd,`+w(5e4*qI+"; // Cambiar si es necesario
$dbname = "masgps";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Truncate de la tabla infoVehiculos
$sql = "TRUNCATE TABLE infoVehiculos";
if ($conn->query($sql) === TRUE) {
    echo "Tabla infoVehiculos truncada correctamente.\n";
} else {
    echo "Error truncando la tabla: " . $conn->error . "\n";
}

$conn->close();

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

$vehiculos_data = json_decode($response, true);
$vehiculos = array_slice($vehiculos_data['response'], 2);

$maxProcesses = 50; // Número máximo de procesos paralelos
$processes = [];

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
    
    // Verificar si centroCosto comienza con 'ACTIVO'
    if (strpos($centroCosto, 'ACTIVO') === 0) {
        $data = array(
            'patente' => $patente,
            'nroInterno' => $nroInterno,
            'centroCosto' => $centroCosto,
            'marcaChasis' => $marcaChasis,
            'modeloChasis' => $modeloChasis,
            'marcaCarroceria' => $marcaCarroceria,
            'modeloCarroceria' => $modeloCarroceria,
            'ano' => $ano,
            'unidadNegocio' => $unidadNegocio,
            'estado' => $estado,
            'flota' => $flota,
            'ubicacion' => $ubicacion
        );

        $jsonData = json_encode($data);
        
        // Limitar el número de procesos paralelos
        while (count($processes) >= $maxProcesses) {
            foreach ($processes as $key => $process) {
                $status = proc_get_status($process);
                if (!$status['running']) {
                    proc_close($process);
                    unset($processes[$key]);
                }
            }
            usleep(100000); // Esperar 0.1 segundos antes de volver a verificar
        }

        // Abrir un nuevo proceso
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin
           1 => array("pipe", "w"),  // stdout
           2 => array("pipe", "w")   // stderr
        );

        $process = proc_open("php insert.php " . escapeshellarg($jsonData), $descriptorspec, $pipes);

        if (is_resource($process)) {
            $processes[] = $process;
        }
    }
}

// Esperar a que todos los procesos terminen
foreach ($processes as $process) {
    proc_close($process);
}

echo "Todos los registros han sido insertados.\n";
?>
