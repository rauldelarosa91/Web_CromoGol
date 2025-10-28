<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartas Base - CromoGol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <link rel="stylesheet" href="CromoGol-css/cartas-base.css">
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
                        <span id="contador-carrito">0</span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="category-page">
        <h1>Cartas Base</h1>
        <div id="catalogo-cartas" class="catalogo-cartas">
        </div>
    </main>

    <footer>
        <p>&copy; 2025 CromoGol</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contadorCarritoSpan = document.getElementById('contador-carrito'); // Obtener el span del contador

            function actualizarContadorCarrito() {
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

            fetch('obtener_cartas.php?tipo=Base')
                .then(response => response.json())
                .then(data => {
                    const catalogoCartas = document.getElementById('catalogo-cartas');
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
                                <form class="form-añadir-carrito" method="post" action="carrito.php">
                                    <input type="hidden" name="agregar" value="true">
                                    <input type="hidden" name="referencia" value="${carta.referencia}">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="add-to-cart-btn" onclick="actualizarContadorCarrito()">
                                        Añadir al carrito
                                    </button>
                                </form>
                            `;
                            catalogoCartas.appendChild(cartaDiv);
                        });

                        // Añadir event listeners a todos los formularios de añadir al carrito
                        const formulariosCarrito = document.querySelectorAll('.form-añadir-carrito');
                        formulariosCarrito.forEach(formulario => {
                            formulario.addEventListener('submit', function(event) {
                                // Verificar si la variable de sesión 'usuario_id' está definida (simulado en el cliente)
                                // En una aplicación real, esta verificación se haría en el servidor.
                                const estaLogueado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;

                                if (!estaLogueado) {
                                    event.preventDefault(); // Evitar que se envíe el formulario
                                    alert('Debes iniciar sesión o registrarte para añadir cartas al carrito.');
                                }
                            });
                        });

                    } else {
                        catalogoCartas.innerHTML = '<p>No hay cartas base disponibles.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar las cartas:', error);
                    catalogoCartas.innerHTML = '<p>Error al cargar las cartas.</p>';
                });
        });
    </script>
    <script src="CromoGol-js/carrito.js"></script>
</body>

</html>