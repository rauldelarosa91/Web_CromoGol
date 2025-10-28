<?php
session_start();
include 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el carrito del usuario
function obtenerCarritoParaFinalizarCompra() {
    $carrito = [];
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
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i", $usuarioId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $carrito[] = $row;
            }
        }
        $stmt->close();
    }
    return $carrito;
}

// Obtener el total del carrito
function obtenerTotalCarritoParaFinalizarCompra() {
    $total = 0;
    $carrito = obtenerCarritoParaFinalizarCompra();
    foreach ($carrito as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
    return $total;
}

$carrito_finalizar = obtenerCarritoParaFinalizarCompra();
$total_finalizar = obtenerTotalCarritoParaFinalizarCompra();

// Procesar la simulación de pago
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simular_pago'])) {
    $usuarioId = $_SESSION['usuario_id'];
    $fecha_pedido = date("Y-m-d H:i:s");
    $estado_pedido = 'pagado'; // Simulamos que el pago fue exitoso
    $direccion_envio = 'Dirección simulada'; // Valor por defecto

    $sql_pedido = "INSERT INTO pedidos (usuario_id, fecha_pedido, estado, total, direccion_envio) VALUES (?, ?, ?, ?, ?)";
    $stmt_pedido = $conn->prepare($sql_pedido);
    $stmt_pedido->bind_param("issss", $usuarioId, $fecha_pedido, $estado_pedido, $total_finalizar, $direccion_envio);

    if ($stmt_pedido->execute()) {
        $pedido_id = $conn->insert_id;

        $carrito_detalles = obtenerCarritoParaFinalizarCompra();
        foreach ($carrito_detalles as $item) {
            $sql_detalle = "INSERT INTO detalles_pedido (pedido_id, referencia_producto, nombre_producto, precio_unitario, cantidad, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_detalle = $conn->prepare($sql_detalle);
            $subtotal = $item['precio'] * $item['cantidad'];
            $stmt_detalle->bind_param("isddid", $pedido_id, $item['referencia'], $item['nombre'], $item['precio'], $item['cantidad'], $subtotal);
            $stmt_detalle->execute();
            $stmt_detalle->close();
        }

        // Vaciar el carrito del usuario
        $sql_vaciar_carrito = "DELETE FROM carrito WHERE usuario_id = ?";
        $stmt_vaciar_carrito = $conn->prepare($sql_vaciar_carrito);
        $stmt_vaciar_carrito->bind_param("i", $usuarioId);
        $stmt_vaciar_carrito->execute();
        $stmt_vaciar_carrito->close();

        header("Location: pedido_confirmado.php?id=" . $pedido_id);
        exit();
    } else {
        echo "Error al registrar el pedido: " . $conn->error;
    }

    $stmt_pedido->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - CromoGol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <link rel="stylesheet" href="CromoGol-css/finalizar-compra.css">
    <style>
        .resumen-pedido {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .resumen-pedido h3 {
            margin-top: 0;
        }
        .resumen-pedido p {
            margin-bottom: 5px;
        }
        .simular-pago-form button.button {
            padding: 10px 20px;
            background-color: #00a000; /* Verde más llamativo para simular pago */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        .simular-pago-form button.button:hover {
            background-color: #007000;
        }
        /* Estilos para el footer */
footer {
    background-color: #333; /* Puedes cambiar el color de fondo */
    color: white;
    padding: 20px 0; /* Añade un poco de padding arriba y abajo */
    text-align: center; /* Centra el texto */
    margin-top: 50px; /* Añade un margen superior para separarlo del contenido */
    width: 100%; /* Ocupa todo el ancho */
    position: relative; /* Asegura que se comporte como bloque */
    bottom: 0; /* Si quieres que esté siempre abajo (puede superponerse en contenido corto) */
}

footer p {
    margin: 0; /* Elimina el margen predeterminado del párrafo */
    font-size: 0.9em; /* Ajusta el tamaño de la fuente si lo deseas */
}
    </style>
</head>
<body>
    <header>
        </header>

    <main class="finalizar-compra-page">
        <h2>Finalizar Compra (Simulación de Pago)</h2>

        <div class="resumen-pedido">
            <h3>Resumen del Pedido</h3>
            <?php if (empty($carrito_finalizar)): ?>
                <p>Tu carrito está vacío.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($carrito_finalizar as $item): ?>
                        <li><?php echo $item['nombre']; ?> x <?php echo $item['cantidad']; ?> - <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €</li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Total: <?php echo number_format($total_finalizar, 2); ?> €</strong></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($carrito_finalizar)): ?>
            <div class="simular-pago-form">
                <form method="post" action="">
                    <button type="submit" name="simular_pago" class="button">Simular Pago Exitoso</button>
                    <p><small>Al hacer clic, se simulará un pago exitoso y se registrará tu pedido.</small></p>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        </footer>
</body>
</html>