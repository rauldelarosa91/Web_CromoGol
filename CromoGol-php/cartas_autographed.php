<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartas Autographed - CromoGol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <link rel="stylesheet" href="CromoGol-css/cartas-autographed.css">
    <style>
        .carta form {
            margin-top: 10px;
        }

        .carta form button.button {
            padding: 3px 10px;
            background-color: #f0f0f0;
            /* Color de fondo gris MUY claro */
            color: #333;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .carta form button.button:hover {
            background-color: #e1e1e1;
            /* Un gris ligeramente más oscuro al pasar el ratón */
        }

        .carta form button.button:active {
            background-color: #d1d1d1;
            /* Un gris aún más oscuro al hacer clic */
        }

        .mensaje-no-logueado {
            color: red;
            font-style: italic;
            margin-top: 5px;
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
                <li><a href="cartas_base.php">Base</a></li>
                <li><a href="cartas_special.php">Special</a></li>
                <li><a href="cartas_rookie.php">Rookie</a></li>
                <li><a href="cartas_rare.php">Rare</a></li>
                <li><a href="cartas_autographed.php">Autographed</a></li>
                <li class="carrito">
                    <a href="carrito.php" title="Ver carrito de compras">
                        <img src="CromoGol-imagenes/carrito.avif" alt="Carrito de compras">
                        <?php
                        session_start();
                        include 'conexion.php';

                        $totalItemsHeader = 0;
                        if (isset($_SESSION['usuario_id'])) {
                            $usuarioId = $_SESSION['usuario_id'];
                            $sql_contador_header = "SELECT SUM(cantidad) AS total_items FROM carrito WHERE usuario_id = ?";
                            $stmt_contador_header = $conn->prepare($sql_contador_header);
                            $stmt_contador_header->bind_param("i", $usuarioId);
                            $stmt_contador_header->execute();
                            $result_contador_header = $stmt_contador_header->get_result();
                            if ($row_contador_header = $result_contador_header->fetch_assoc()) {
                                $totalItemsHeader = $row_contador_header['total_items'] ?: 0;
                            }
                            $stmt_contador_header->close();
                        }
                        $conn->close();
                        echo '<span id="contador-carrito">' . $totalItemsHeader . '</span>';
                        ?>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="category-page">
        <h1>Cartas Autographed</h1>
        <div id="catalogo-cartas" class="catalogo-cartas">
            <?php
            include 'conexion.php';
            $tipoCarta = 'Autographed';
            $sql_cartas = "SELECT * FROM productos WHERE tipo_carta = ?";
            $stmt_cartas = $conn->prepare($sql_cartas);
            $stmt_cartas->bind_param("s", $tipoCarta);
            $stmt_cartas->execute();
            $result_cartas = $stmt_cartas->get_result();

            if ($result_cartas->num_rows > 0) {
                while ($carta = $result_cartas->fetch_assoc()) {
                    echo '<div class="carta">';
                    echo '<h3>' . htmlspecialchars($carta['nombre']) . '</h3>';
                    echo '<p>Referencia: ' . htmlspecialchars($carta['referencia']) . '</p>';
                    echo '<p>Descripción: ' . htmlspecialchars($carta['descripcion']) . '</p>';
                    echo '<p>Precio: ' . htmlspecialchars($carta['precio']) . ' €</p>';
                    echo '<p>Liga: ' . htmlspecialchars($carta['liga']) . '</p>';
                    echo '<p>Equipo: ' . htmlspecialchars($carta['equipo']) . '</p>';
                    echo '<p>Temporada: ' . htmlspecialchars($carta['temporada']) . '</p>';
                    echo '<p>Tipo: ' . htmlspecialchars($carta['tipo_carta']) . '</p>';
                    echo '<p>Posición: ' . htmlspecialchars($carta['posicion']) . '</p>';
                    echo '<form class="form-añadir-carrito" method="post" action="carrito.php">';
                    echo '<input type="hidden" name="agregar" value="true">';
                    echo '<input type="hidden" name="referencia" value="' . htmlspecialchars($carta['referencia']) . '">';
                    echo '<button type="submit" class="button">Añadir al carrito</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>No hay cartas autographed disponibles.</p>';
            }
            $stmt_cartas->close();
            $conn->close();
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 CromoGol tu tienda de cartas de futbol</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formulariosCarrito = document.querySelectorAll('.form-añadir-carrito');
            formulariosCarrito.forEach(formulario => {
                formulario.addEventListener('submit', function(event) {
                    const estaLogueado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
                    if (!estaLogueado) {
                        event.preventDefault();
                        alert('Debes iniciar sesión o registrarte para añadir cartas al carrito.');
                    }
                });
            });
        });
    </script>
</body>

</html>