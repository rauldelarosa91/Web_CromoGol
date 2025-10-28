<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CromoGol - Tu Tienda de Cartas de Fútbol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <style>
        /* Estilos básicos para las cartas (puedes moverlos a tu CSS si prefieres) */
        .carta {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
        }

        .btn-anadir-carrito {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-anadir-carrito:hover {
            background-color: #0056b3;
        }

        .cartas-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .boton-verde {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            /* Verde */
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .boton-verde:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <a href="index.php">
                <h1>CromoGol</h1>
            </a>
        </div>
        <nav class="main-navigation">
            <ul class="main-menu open" id="main-menu">
                <li><a href="mostrar_cartas.php?categoria=base">Base</a></li>
                <li><a href="mostrar_cartas.php?categoria=special">Special</a></li>
                <li><a href="mostrar_cartas.php?categoria=rookie">Rookie</a></li>
                <li><a href="mostrar_cartas.php?categoria=rare">Rare</a></li>
                <li><a href="mostrar_cartas.php?categoria=autographed">Autographed</a></li>
                <?php
                session_start();
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
                        <?php
                        $totalItems = 0;
                        if (isset($_SESSION['usuario_id'])) {
                            include 'conexion.php';
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
                            $conn->close();
                        }
                        echo '<span id="contador-carrito">' . $totalItems . '</span>';
                        ?>
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <main class="home-page">
        <section class="hero">
            <div class="hero-text">
                <h2>Descubre un amplio catálogo de las mejores cartas de fútbol</h2>
                <img src="CromoGol-imagenes/coleccion_futbol.avif" alt="Topps Simplicidad">
                <br>
                <a href="mostrar_cartas.php?categoria=base" class="boton-verde">Ver Cartas Base</a>
            </div>
        </section>
        <section class="featured-categories">
            <h3>Nuestras Cartas Destacadas</h3>
            <div class="cartas-container" id="cartas-container">
                <ul>
                    <li><a href="mostrar_cartas.php?categoria=base">Cartas Base</a></li>
                    <li><a href="mostrar_cartas.php?categoria=special">Cartas Special</a></li>
                    <li><a href="mostrar_cartas.php?categoria=rookie">Cartas Rookie</a></li>
                    <li><a href="mostrar_cartas.php?categoria=rare">Cartas Rare</a></li>
                    <li><a href="mostrar_cartas.php?categoria=autographed">Cartas Autographed</a></li>
                </ul>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 CromoGol tu tienda de cartas </p>
    </footer>
    <script src="CromoGol-js/carrito.js"></script>
</body>

</html>