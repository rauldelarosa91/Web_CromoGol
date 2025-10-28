<?php
// obtener_cartas.php

// Credenciales de la base de datos (ajusta con tus datos)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "CromoGol";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta para obtener todas las cartas
    $sql = "SELECT referencia, nombre, precio FROM productos"; // Ajusta la consulta y las columnas
    $result = $conn->query($sql);

    $cartas = array();
    if ($result->num_rows > 0) {
        // Almacenar cada fila como un array asociativo
        while ($row = $result->fetch_assoc()) {
            $cartas[] = $row;
        }
    }

    // Cerrar la conexión
    $conn->close();

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($cartas);

} catch (Exception $e) {
    // En caso de error, devolver un JSON con el error
    header('Content-Type: application/json');
    echo json_encode(array("error" => $e->getMessage()));
}
?>