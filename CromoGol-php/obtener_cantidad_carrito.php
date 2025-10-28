<?php
session_start();
include 'conexion.php';

if (isset($_SESSION['usuario_id'])) {
    $usuarioId = $_SESSION['usuario_id'];

    $sql = "SELECT SUM(cantidad) AS total_items FROM carrito WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_items = $row['total_items'] ?? 0; // Si es null, devuelve 0

    echo json_encode(['success' => true, 'total_items' => $total_items]);
} else {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
}
$conn->close();
?>
