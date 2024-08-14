<?php

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

// Verificar si se envió una consulta SQL
if (isset($_POST['query'])) {
    $sql = $_POST['query'];

    // Ejecutar la consulta
    if ($conn->query($sql) === TRUE) {
        echo "Registro insertado correctamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "No se recibió ninguna consulta SQL.";
}

$conn->close();
?>
