<?php

function trim_var($variable)
{
    if (is_string($variable)) {
        return trim($variable);
    } else {
        return $variable;
    }
}

$url = "https://api.pullman.cl/srv-vehiculo-core-web/rest/informes/obtenerInformeVehiculosExcel";

$headers = [
    'Content-Type: application/json'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
$response = curl_exec($ch);
curl_close($ch);

$vehiculos_data = json_decode($response, true);
$vehiculos = array_slice($vehiculos_data['response'], 2);

$servername = "localhost"; // Agregar tu servidor
$username = "root"; // Agregar tu nombre de usuario
$password = ""; // Agregar tu contraseña
$dbname = "pullman_tag";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$i=0;

foreach ($vehiculos as $v) {
    $patente = trim_var(isset($v['prop01']) ? $v['prop01'] : "");
    $nroInterno = trim_var(isset($v['prop02']) ? $v['prop02'] : "");
    $tipoOperacional = trim_var(isset($v['prop03']) ? $v['prop03'] : "");
    $tipoRegisCivil = trim_var(isset($v['prop04']) ? $v['prop04'] : "");
    $marcaChasis = trim_var(isset($v['prop05']) ? $v['prop05'] : "");
    $modeloChasis = trim_var(isset($v['prop06']) ? $v['prop06'] : "");
    $marcaCarroceria = trim_var(isset($v['prop07']) ? $v['prop07'] : "");
    $modeloCarroceria = trim_var(isset($v['prop08']) ? $v['prop08'] : "");
    $ano = trim_var(isset($v['prop09']) ? $v['prop09'] : "");
    $nroChasis = trim_var(isset($v['prop10']) ? $v['prop10'] : "");
    $nroMotor = trim_var(isset($v['prop11']) ? $v['prop11'] : "");
    $nroCarroceria = trim_var(isset($v['prop12']) ? $v['prop12'] : "");
    $estado = trim_var(isset($v['prop13']) ? $v['prop13'] : "");
    $empresaPropietaria = trim_var(isset($v['prop14']) ? $v['prop14'] : "");
    $empresaTenedora = trim_var(isset($v['prop15']) ? $v['prop15'] : "");
    $tipoPropiedad = trim_var(isset($v['prop16']) ? $v['prop16'] : "");
    $unidadNegocio = trim_var(isset($v['prop17']) ? $v['prop17'] : "");
    $ubicacion = trim_var(isset($v['prop18']) ? $v['prop18'] : "");
    $centroCosto = trim_var(isset($v['prop19']) ? $v['prop19'] : "");
    $descCentroCosto = trim_var(isset($v['prop20']) ? $v['prop20'] : "");
    $flota = trim_var(isset($v['prop21']) ? $v['prop21'] : "");
    $descFlota = trim_var(isset($v['prop22']) ? $v['prop22'] : "");
    $valorCuotaPromedio = trim_var(isset($v['prop23']) ? $v['prop23'] : "");
    $observacion = trim_var(isset($v['prop24']) ? $v['prop24'] : "");
    $cav = trim_var(isset($v['prop25']) ? $v['prop25'] : "");
    $anotacionesVigentes = trim_var(isset($v['prop26']) ? $v['prop26'] : "");
    $revisionTecnica = trim_var(isset($v['prop27']) ? $v['prop27'] : "");
    $vencimientoRevTecnica = trim_var(isset($v['prop28']) ? $v['prop28'] : "");
    $permisoDeCirculacion = trim_var(isset($v['prop29']) ? $v['prop29'] : "");
    $vencimientoPermisoDeCirculacion = trim_var(isset($v['prop30']) ? $v['prop30'] : "");
    $seguro = trim_var(isset($v['prop31']) ? $v['prop31'] : "");
    $vencimientoSeguro = trim_var(isset($v['prop32']) ? $v['prop32'] : "");
    $decreto80 = trim_var(isset($v['prop33']) ? $v['prop33'] : "");
    $vencimientoDecreto80 = trim_var(isset($v['prop34']) ? $v['prop34'] : "");
    $ds212 = trim_var(isset($v['prop35']) ? $v['prop35'] : "");
    $vencimientoDs212 = trim_var(isset($v['prop36']) ? $v['prop36'] : "");
    $imagen = trim_var(isset($v['prop37']) ? $v['prop37'] : "");

    $data[] = [
        $patente,
        $nroInterno,
        $tipoOperacional,
        $tipoRegisCivil,
        $marcaChasis,
        $modeloChasis,
        $marcaCarroceria,
        $modeloCarroceria,
        $ano,
        $centroCosto,
        $descCentroCosto,
        $unidadNegocio,
        $estado,
        $empresaPropietaria,
        $empresaTenedora,
        $flota,
        $nroChasis,
        $nroMotor,
        $nroCarroceria,
        $ubicacion,
        $observacion,
        $tipoPropiedad
    ];

    $total[$i] = $data;
    $i++;
}

echo json_encode($total, http_response_code(200));