<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$pedido_id = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - CromoGol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <link rel="stylesheet" href="CromoGol-css/pedido-confirmado.css">
    <style>
        .confirmacion-pedido {
            text-align: center;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .confirmacion-pedido h2 {
            color: #28a745;
        }
        .confirmacion-pedido p {
            margin-bottom: 10px;
        }
        .confirmacion-pedido a.button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            text-decoration: none;
        }
        .confirmacion-pedido a.button:hover {
            background-color: #0056b3;
        }
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
                        <span id="contador-carrito"><?php
                            include 'conexion.php';
                            $totalItems = 0;
                            if (isset($_SESSION['usuario_id'])) {
                                $usuarioId = $_SESSION['usuario_id'];
                                $sql_contador = "SELECT SUM(cantidad) AS total_items FROM carrito WHERE usuario_id = ?";
                                $stmt_contador = $conn->prepare($sql_contador);
                                $stmt_contador->bind_param("i", $usuarioId);
                                $stmt_contador->execute();
                                $result_contador = $stmt_contador->get_result();
                                if ($row_contador = $result_contador->fetch_assoc()) {
                                    $totalItems = $row_contador['total_items'] ?: 0;
                                }
                                $stmt_contador->close();
                            }
                            $conn->close();
                            echo $totalItems;
                            ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="pedido-confirmado-page">
        <div class="confirmacion-pedido">
            <h2>¡Pedido Confirmado!</h2>
            <p>Gracias por tu compra. Tu número de pedido es: <strong><?php echo $pedido_id; ?></strong></p>
            <p>En breve nos pondremos en contacto contigo para coordinar el envío.</p>
            <a href="index.php" class="button">Volver a la Tienda</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 CromoGol tu tienda de cartas </p>
    </footer>
</body>
</html>