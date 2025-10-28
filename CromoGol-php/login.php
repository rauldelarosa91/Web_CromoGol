<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - CromoGol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <link rel="stylesheet" href="CromoGol-css/login.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php"><h1>CromoGol</h1></a>
        </div>
        <nav class="main-navigation">
            <ul class="main-menu open" id="main-menu">
                <li><a href="cartas_base.php">Base</a></li>
                <li><a href="cartas_special.php">Special</a></li>
                <li><a href="cartas_rookie.php">Rookie</a></li>
                <li><a href="cartas_rare.php">Rare</a></li>
                <li><a href="cartas_autographed.php">Autographed</a></li>
                <li><a href="registro.php">Registrarse</a></li>
                <li class="carrito">
                    <a href="carrito.php" title="Ver carrito de compras">
                        <img src="CromoGol-imagenes/carrito.avif" alt="Carrito de compras">
                        <?php
                        session_start();
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
                        echo $totalItems;
                        ?>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php
        if (isset($_POST['login'])) {
            include 'conexion.php';

            $nombre_usuario = $_POST['nombre_usuario'];
            $contrasena = $_POST['contrasena'];

            $stmt = $conn->prepare("SELECT usuario_id, contrasena FROM user WHERE nombre_usuario = ?");
            $stmt->bind_param("s", $nombre_usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (password_verify($contrasena, $row['contrasena'])) {
                    // Inicio de sesión exitoso
                    $_SESSION['usuario_id'] = $row['usuario_id'];
                    echo '<p class="success-message">Inicio de sesión exitoso. Redirigiendo...</p>';
                    header("refresh:2;url=index.php"); // Redirigir a la página principal después de 2 segundos
                } else {
                    echo '<p class="error-message">Contraseña incorrecta.</p>';
                }
            } else {
                echo '<p class="error-message">Nombre de usuario no encontrado.</p>';
            }

            $stmt->close();
            $conn->close();
        }
        ?>
        <form method="post">
            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <div class="form-group">
                <button type="submit" name="login">Iniciar Sesión</button>
            </div>
            <p>¿No tienes una cuenta? <a href="registro.php">Regístrate</a></p>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 CromoGol tu tienda de cartas </p>
    </footer>
</body>
</html>