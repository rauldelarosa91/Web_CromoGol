<?php
// carrito.php
session_start();
include 'conexion.php';

// Función para agregar un producto al carrito
function agregarAlCarrito($referencia, $cantidad = 1) {
    if (isset($_SESSION['usuario_id'])) {
        $usuarioId = $_SESSION['usuario_id'];
        $sql_verificar = "SELECT cantidad FROM carrito WHERE usuario_id = ? AND referencia = ?";
        $stmt_verificar = $GLOBALS['conn']->prepare($sql_verificar);
        $stmt_verificar->bind_param("is", $usuarioId, $referencia);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();
        if ($result_verificar->num_rows > 0) {
            $row_verificar = $result_verificar->fetch_assoc();
            $nuevaCantidad = $row_verificar['cantidad'] + $cantidad;
            $sql_actualizar = "UPDATE carrito SET cantidad = ? WHERE usuario_id = ? AND referencia = ?";
            $stmt_actualizar = $GLOBALS['conn']->prepare($sql_actualizar);
            $stmt_actualizar->bind_param("iis", $nuevaCantidad, $usuarioId, $referencia);
            $stmt_actualizar->execute();
        } else {
            $sql_insertar = "INSERT INTO carrito (usuario_id, referencia, cantidad) VALUES (?, ?, ?)";
            $stmt_insertar = $GLOBALS['conn']->prepare($sql_insertar);
            $stmt_insertar->bind_param("isi", $usuarioId, $referencia, $cantidad);
            $stmt_insertar->execute();
        }
        $stmt_verificar->close();
    }
}

// Función para eliminar un producto del carrito
function eliminarDelCarrito($referencia) {
    if (isset($_SESSION['usuario_id'])) {
        $usuarioId = $_SESSION['usuario_id'];
        $sql_eliminar = "DELETE FROM carrito WHERE usuario_id = ? AND referencia = ?";
        $stmt_eliminar = $GLOBALS['conn']->prepare($sql_eliminar);
        $stmt_eliminar->bind_param("is", $usuarioId, $referencia);
        $stmt_eliminar->execute();
        $stmt_eliminar->close();
    }
}

// Función para cambiar la cantidad de un producto en el carrito
function cambiarCantidad($referencia, $cambio) {
    if (isset($_SESSION['usuario_id'])) {
        $usuarioId = $_SESSION['usuario_id'];
        $sql_verificar = "SELECT cantidad FROM carrito WHERE usuario_id = ? AND referencia = ?";
        $stmt_verificar = $GLOBALS['conn']->prepare($sql_verificar);
        $stmt_verificar->bind_param("is", $usuarioId, $referencia);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();
        if ($result_verificar->num_rows > 0) {
            $row_verificar = $result_verificar->fetch_assoc();
            $nuevaCantidad = $row_verificar['cantidad'] + $cambio;
            if ($nuevaCantidad > 0) {
                $sql_actualizar = "UPDATE carrito SET cantidad = ? WHERE usuario_id = ? AND referencia = ?";
                $stmt_actualizar = $GLOBALS['conn']->prepare($sql_actualizar);
                $stmt_actualizar->bind_param("iis", $nuevaCantidad, $usuarioId, $referencia);
                $stmt_actualizar->execute();
                $stmt_actualizar->close();
            } else {
                eliminarDelCarrito($referencia);
            }
        }
        $stmt_verificar->close();
    }
}

// Obtener el contenido del carrito para mostrarlo
function obtenerCarrito() {
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

// Obtener la cantidad total de items en el carrito
function obtenerTotalItemsCarrito() {
    $totalItems = 0;
    if (isset($_SESSION['usuario_id'])) {
        $usuarioId = $_SESSION['usuario_id'];
        $sql_contador = "SELECT SUM(cantidad) AS total_items FROM carrito WHERE usuario_id = ?";
        $stmt_contador = $GLOBALS['conn']->prepare($sql_contador);
        $stmt_contador->bind_param("i", $usuarioId);
        $stmt_contador->execute();
        $result_contador = $stmt_contador->get_result();
        if ($row_contador = $result_contador->fetch_assoc()) {
            $totalItems = $row_contador['total_items'] ?: 0;
        }
        $stmt_contador->close();
    }
    return $totalItems;
}

// Procesar acciones del carrito
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['agregar'])) {
        $referencia = $_POST['referencia'];
        $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
        agregarAlCarrito($referencia, $cantidad);
        $pagina_anterior = $_SERVER['HTTP_REFERER'];
        header("Location: " . $pagina_anterior);
        exit();
    } elseif (isset($_POST['eliminar'])) {
        $referencia = $_POST['eliminar'];
        eliminarDelCarrito($referencia);
        $pagina_anterior = $_SERVER['HTTP_REFERER'];
        header("Location: " . $pagina_anterior);
        exit();
    } elseif (isset($_POST['cambiar_cantidad'])) {
        $referencia = $_POST['cambiar_cantidad'];
        $cambio = intval($_POST['cambio']);
        cambiarCantidad($referencia, $cambio);
        header("Location: carrito.php");
        exit();
    }
}

$carrito = obtenerCarrito();
$totalItems = obtenerTotalItemsCarrito();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - CromoGol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <link rel="stylesheet" href="CromoGol-css/carrito.css">
    <style>
        .tabla-carrito { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .tabla-carrito th, .tabla-carrito td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .tabla-carrito th { background-color: #f2f2f2; }
        .cantidad-container { display: flex; align-items: center; gap: 5px; }
        .btn-cambiar-cantidad, .btn-eliminar { padding: 5px 10px; cursor: pointer; border: 1px solid #ccc; border-radius: 3px; background-color: #eee; }
        .btn-cambiar-cantidad:hover, .btn-eliminar:hover { background-color: #ddd; }
        .carrito-vacio { text-align: center; padding: 20px; font-style: italic; color: #777; }
        .finalizar-compra-btn { display: block; margin-top: 20px; padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; text-align: center; text-decoration: none; }
        .finalizar-compra-btn:hover { background-color: #1e7e34; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php"><h1>CromoGol</h1></a>
        </div>
        <nav class="main-navigation">
            <ul class="main-menu open" id="main-menu">
                <li><a href="mostrar_cartas.php?categoria=base">Base</a></li>
                <li><a href="mostrar_cartas.php?categoria=special">Special</a></li>
                <li><a href="mostrar_cartas.php?categoria=rookie">Rookie</a></li>
                <li><a href="mostrar_cartas.php?categoria=rare">Rare</a></li>
                <li><a href="mostrar_cartas.php?categoria=autographed">Autographed</a></li>
                <?php
                if (isset($_SESSION['usuario_id'])) {
                    echo '<li><a href="logout.php">Cerrar Sesión</a></li>';
                } else {
                    echo '<li><a href="login.php">Iniciar Sesión</a></li>';
                    echo '<li><a href="registro.php">Registrarse</a></li>';
                }
                ?>
                <li class="carrito">
                    <a href="carrito.php" title="Ver carrito de compras">
                        <img src="CromoGol-imagenes/carrito.avif" alt="Carrito de compras">
                        <span id="contador-carrito"><?php echo $totalItems; ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="carrito-page">
        <h2>Tu Carrito de Compras</h2>
        <div id="lista-carrito">
            <?php if (empty($carrito)): ?>
                <p class="carrito-vacio">Tu carrito está vacío.</p>
            <?php else: ?>
                <table class="tabla-carrito">
                    <thead>
                        <tr>
                            <th scope="col">Producto</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($carrito as $item):
                            ?>
                            <tr>
                                <td><?php echo $item['nombre']; ?></td>
                                <td><?php echo $item['precio']; ?> €</td>
                                <td>
                                    <div class="cantidad-container">
                                        <form method="post">
                                            <input type="hidden" name="cambiar_cantidad" value="<?php echo $item['referencia']; ?>">
                                            <input type="hidden" name="cambio" value="-1">
                                            <button type="submit" class="btn-cambiar-cantidad btn-menos" aria-label="Disminuir cantidad">-</button>
                                        </form>
                                        <span class="cantidad"><?php echo $item['cantidad']; ?></span>
                                        <form method="post">
                                            <input type="hidden" name="cambiar_cantidad" value="<?php echo $item['referencia']; ?>">
                                            <input type="hidden" name="cambio" value="1">
                                            <button type="submit" class="btn-cambiar-cantidad btn-mas" aria-label="Aumentar cantidad">+</button>
                                        </form>
                                    </div>
                                </td>
                                <td><?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €</td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="eliminar" value="<?php echo $item['referencia']; ?>">
                                        <button type="submit" class="btn-eliminar" aria-label="Eliminar producto">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                            $total += $item['precio'] * $item['cantidad'];
                        endforeach;
                        ?>
                    </tbody>
                </table>
                <p>Total: <span id="total-carrito"><?php echo number_format($total, 2); ?></span> €</p>
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <a href="finalizar_compra.php" class="finalizar-compra-btn">Finalizar Compra</a>
                <?php else: ?>
                    <p>Debes <a href="login.php">iniciar sesión</a> para poder finalizar la compra.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 CromoGol tu tienda de cartas </p>
    </footer>
</body>
</html>