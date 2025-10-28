<?php
// mostrar_cartas.php
session_start();
include 'conexion.php';

// Obtener la categoría de la URL
$categoria = $_GET['categoria'] ?? 'Base'; // 'Base' como valor predeterminado

// Definir un array de categorías válidas (deben coincidir con los valores en tu base de datos)
$categorias_validas = ['base', 'special', 'rookie', 'rare', 'autographed'];

if (!in_array($categoria, $categorias_validas)) {
    echo '<p class="error">Categoría no válida.</p>';
    exit;
}

// Consulta para obtener las cartas de la categoría especificada
$sql = "SELECT referencia, nombre, precio FROM productos WHERE tipo_carta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $categoria);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CromoGol - Cartas de Fútbol <?php echo ucfirst(strtolower($categoria)); ?></title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <style>
        .carta {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
        }

        .btn-anadir-carrito {
            background-color: #007bff;
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
                            $sql_contador = "SELECT SUM(cantidad) AS total_items FROM carrito WHERE usuario_id = ?";
                            $stmt_contador = $conn->prepare($sql_contador);
                            $stmt_contador->bind_param("i", $_SESSION['usuario_id']);
                            $stmt_contador->execute();
                            $result_contador = $stmt_contador->get_result();
                            if ($row_contador = $result_contador->fetch_assoc()) {
                                $totalItems = $row_contador['total_items'] ?: 0;
                            }
                            $stmt_contador->close();
                        }
                        echo '<span id="contador-carrito">' . $totalItems . '</span>';
                        ?>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="category-page">
        <h2>Cartas de Fútbol <?php echo ucfirst(strtolower($categoria)); ?></h2>
        <div class="cartas-container" id="cartas-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="carta">';
                    echo '<h3>' . htmlspecialchars($row['nombre']) . '</h3>';
                    echo '<p>Referencia: ' . htmlspecialchars($row['referencia']) . '</p>';
                    echo '<p>Precio: ' . htmlspecialchars($row['precio']) . ' €</p>';
                    echo '<button class="btn-anadir-carrito"';
                    echo ' data-referencia="' . htmlspecialchars($row['referencia']) . '"';
                    echo ' data-nombre="' . htmlspecialchars($row['nombre']) . '"';
                    echo ' data-precio="' . htmlspecialchars($row['precio']) . '">';
                    echo 'Añadir al carrito';
                    echo '</button>';
                    echo '</div>';
                }
            } else {
                echo '<p>No se encontraron cartas en esta categoría.</p>';
            }
            $conn->close();
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 CromoGol tu tienda de cartas </p>
    </footer>
    <script src="CromoGol-js/carrito.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const botonesAnadir = document.querySelectorAll('.btn-anadir-carrito');
            botonesAnadir.forEach(boton => {
                boton.addEventListener('click', function() {
                    const referencia = this.dataset.referencia;
                    fetch('agregar_al_carrito.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `referencia=${referencia}`,
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Producto añadido al carrito!');
                                const contadorCarrito = document.getElementById('contador-carrito');
                                if (contadorCarrito && data.total_items !== undefined) {
                                    contadorCarrito.textContent = data.total_items;
                                }
                            } else {
                                alert('Error: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error de red:', error);
                            alert('Error de red al añadir al carrito.');
                        });
                });
            });
        });
    </script>
</body>

</html>