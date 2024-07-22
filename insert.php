<?php
if ($argc < 2) {
    die("No se proporcionaron datos para insertar.");
}

$data = json_decode($argv[1], true);

$servername = "ls-3c0c538286def4da7f8273aa5531e0b6eee0990c.cylsiewx0zgx.us-east-1.rds.amazonaws.com"; // Cambiar si es necesario
$username = "dbmasteruser"; // Cambiar si es necesario
$password = "eF5D;6VzP$^7qDryBzDd,`+w(5e4*qI+"; // Cambiar si es necesario
$dbname = "masgps";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "INSERT INTO infoVehiculos (patente, nroInterno, centroCosto, marcaChasis, modeloChasis, marcaCarroceria, modeloCarroceria, ano, unidadNegocio, estado, flota, ubicacion, descCentroCosto) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssssss", $data['patente'], $data['nroInterno'], $data['centroCosto'], $data['marcaChasis'], $data['modeloChasis'], $data['marcaCarroceria'], $data['modeloCarroceria'], $data['ano'], $data['unidadNegocio'], $data['estado'], $data['flota'], $data['ubicacion'], $data['descCentroCosto']);

if ($stmt->execute()) {
    echo "Registro insertado correctamente.\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>
