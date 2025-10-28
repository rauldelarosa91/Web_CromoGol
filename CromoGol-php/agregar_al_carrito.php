<?php
session_start();
include 'conexion.php';

if (isset($_POST['referencia']) && isset($_SESSION['usuario_id'])) {
    $referencia = $_POST['referencia'];
    $usuarioId = $_SESSION['usuario_id'];
    $cantidad = 1; // Cantidad por defecto

    // Función para obtener la cantidad total de items en el carrito
    function obtenerTotalItemsCarrito($conn, $usuarioId)
    {
        $sql_total = "SELECT SUM(cantidad) AS total_items FROM carrito WHERE usuario_id = ?";
        $stmt_total = $conn->prepare($sql_total);
        $stmt_total->bind_param("i", $usuarioId);
        $stmt_total->execute();
        $result_total = $stmt_total->get_result();
        $row_total = $result_total->fetch_assoc();
        $stmt_total->close();
        return $row_total['total_items'] ?: 0;
    }

    // Verificar si el producto existe
    $sql_producto = "SELECT 1 FROM productos WHERE referencia = ?";
    $stmt_producto = $conn->prepare($sql_producto);
    $stmt_producto->bind_param("s", $referencia);
    $stmt_producto->execute();
    if ($stmt_producto->fetch()) {
        $stmt_producto->close();

        // Verificar si el producto ya está en el carrito
        $sql_carrito = "SELECT cantidad FROM carrito WHERE usuario_id = ? AND referencia = ?";
        $stmt_carrito = $conn->prepare($sql_carrito);
        $stmt_carrito->bind_param("is", $usuarioId, $referencia);
        $stmt_carrito->execute();
        $result_carrito = $stmt_carrito->get_result();

        if ($result_carrito->num_rows > 0) {
            // Actualizar la cantidad
            $row_carrito = $result_carrito->fetch_assoc();
            $nueva_cantidad = $row_carrito['cantidad'] + $cantidad;
            $sql_update = "UPDATE carrito SET cantidad = ? WHERE usuario_id = ? AND referencia = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("iis", $nueva_cantidad, $usuarioId, $referencia);
            if ($stmt_update->execute()) {
                $totalItems = obtenerTotalItemsCarrito($conn, $usuarioId);
                echo json_encode(['success' => true, 'mensaje' => 'Cantidad actualizada', 'total_items' => $totalItems]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al actualizar la cantidad: ' . $stmt_update->error]);
            }
            $stmt_update->close();
        } else {
            // Insertar el producto en el carrito
            $sql_insert = "INSERT INTO carrito (usuario_id, referencia, cantidad) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isi", $usuarioId, $referencia, $cantidad);
            if ($stmt_insert->execute()) {
                $totalItems = obtenerTotalItemsCarrito($conn, $usuarioId);
                echo json_encode(['success' => true, 'mensaje' => 'Producto añadido al carrito', 'total_items' => $totalItems]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al añadir al carrito: ' . $stmt_insert->error]);
            }
            $stmt_insert->close();
        }
        $stmt_carrito->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'El producto no existe']);
        $stmt_producto->close();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Debes iniciar sesión']);
}
$conn->close();
