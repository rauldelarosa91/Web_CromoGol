<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartas Special - CromoGol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <link rel="stylesheet" href="CromoGol-css/cartas-special.css">
    <style>
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
        <h1>Cartas Special</h1>
        <div id="catalogo-cartas" class="catalogo-cartas">
        </div>
    </main>

    <footer>
        <p>&copy; 2025 CromoGol</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const catalogoCartas = document.getElementById('catalogo-cartas');

            fetch('obtener_cartas.php?tipo=Special')
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        data.forEach(carta => {
                            const cartaDiv = document.createElement('div');
                            cartaDiv.classList.add('carta');
                            cartaDiv.innerHTML = `
                                <h3>${carta.nombre}</h3>
                                <p>Referencia: ${carta.referencia}</p>
                                <p>Descripción: ${carta.descripcion}</p>
                                <p>Precio: ${carta.precio} €</p>
                                <p>Liga: ${carta.liga}</p>
                                <p>Equipo: ${carta.equipo}</p>
                                <p>Temporada: ${carta.temporada}</p>
                                <p>Tipo: ${carta.tipo_carta}</p>
                                <p>Posición: ${carta.posicion}</p>
                                <button class="add-to-cart-btn"
                                        data-nombre="${carta.nombre}"
                                        data-precio="${carta.precio}"
                                        data-referencia="${carta.referencia}">
                                    Añadir al carrito
                                </button>
                            `;
                            catalogoCartas.appendChild(cartaDiv);
                        });

                        const botonesAñadirCarrito = document.querySelectorAll('.add-to-cart-btn');
                        botonesAñadirCarrito.forEach(boton => {
                            boton.addEventListener('click', function() {
                                const estaLogueado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
                                if (!estaLogueado) {
                                    alert('Debes iniciar sesión o registrarte para añadir cartas al carrito.');
                                } else {
                                    const nombre = this.dataset.nombre;
                                    const precio = this.dataset.precio;
                                    const referencia = this.dataset.referencia;
                                    // Aquí puedes añadir la lógica para enviar la información al carrito
                                    // (probablemente mediante una petición fetch a un archivo PHP).
                                    console.log(`Añadiendo ${nombre} (${referencia}) al carrito.`);
                                    // Por ejemplo:
                                    /*
                                    fetch('carrito.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: `agregar=true&referencia=${referencia}&cantidad=1`
                                    })
                                    .then(response => response.text())
                                    .then(data => {
                                        console.log(data);
                                        // Actualizar el contador del carrito si es necesario
                                        actualizarContadorCarrito();
                                    })
                                    .catch(error => {
                                        console.error('Error al añadir al carrito:', error);
                                    });
                                    */
                                }
                            });
                        });

                    } else {
                        catalogoCartas.innerHTML = '<p>No hay cartas special disponibles.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar las cartas:', error);
                    catalogoCartas.innerHTML = '<p>Error al cargar las cartas.</p>';
                });

            function actualizarContadorCarrito() {
                const contadorCarritoSpan = document.getElementById('contador-carrito');
                fetch('obtener_cantidad_carrito.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            contadorCarritoSpan.textContent = data.total_items;
                        } else {
                            console.error('Error al obtener la cantidad del carrito:', data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error de red al obtener la cantidad del carrito:', error);
                    });
            }

            // Llama a la función para mostrar la cantidad inicial
            actualizarContadorCarrito();
        });
    </script>
    <script src="js/carrito.js"></script>
</body>

</html>