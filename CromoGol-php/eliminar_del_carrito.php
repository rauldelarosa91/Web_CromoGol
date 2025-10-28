<?php
// eliminar_del_carrito.php
session_start();
include 'conexion.php';

if (isset($_POST['referencia']) && isset($_SESSION['usuario_id'])) {
    $referencia = $_POST['referencia'];
    $usuarioId = $_SESSION['usuario_id'];

    error_log("eliminar_del_carrito.php: referencia=$referencia, usuarioId=$usuarioId");

    $sql_delete = "DELETE FROM carrito WHERE usuario_id = ? AND referencia = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("is", $usuarioId, $referencia);

    if ($stmt_delete->execute()) {
        echo json_encode(['success' => true, 'mensaje' => 'Producto eliminado']);
        error_log("eliminar_del_carrito.php: Producto eliminado.");
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar el producto: ' . $stmt_delete->error]);
        error_log("eliminar_del_carrito.php: Error al eliminar el producto: " . $stmt_delete->error);
    }
    $stmt_delete->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Parámetros incorrectos']);
    error_log("eliminar_del_carrito.php: Parámetros incorrectos.");
}
$conn->close();
?>
