<?php
session_start();
include 'conexion.php';

if (isset($_SESSION['usuario_id'])) {
    $usuarioId = $_SESSION['usuario_id'];

    $sql = "SELECT
                p.referencia,
                p.nombre,
                p.precio,
                c.cantidad
            FROM carrito c
            JOIN productos p ON c.referencia = p.referencia
            WHERE c.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $result = $stmt->get_result();
    $carrito = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $carrito[] = $row;
        }
        echo json_encode(['success' => true, 'carrito' => $carrito]);
    } else {
        echo json_encode(['success' => true, 'carrito' => []]); // Devuelve un carrito vacío, no un error
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
}
$conn->close();
?>