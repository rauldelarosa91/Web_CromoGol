<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - CromoGol</title>
    <link rel="stylesheet" href="CromoGol-css/CromoGol.css">
    <link rel="stylesheet" href="CromoGol-css/registro.css">
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
                <li><a href="login.php">Iniciar Sesión</a></li>
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

    <div class="registro-container">
        <h2>Registro de Nuevo Usuario</h2>
        <?php
        if (isset($_POST['registrar'])) {
            include 'conexion.php';

            $nombre_usuario = $_POST['nombre_usuario'];
            $email = $_POST['email'];
            $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Hash de la contraseña por seguridad

            // Verificar si el nombre de usuario o el email ya existen
            $stmt_verificar = $conn->prepare("SELECT usuario_id FROM user WHERE nombre_usuario = ? OR email = ?");
            $stmt_verificar->bind_param("ss", $nombre_usuario, $email);
            $stmt_verificar->execute();
            $result_verificar = $stmt_verificar->get_result();

            if ($result_verificar->num_rows > 0) {
                echo '<p class="error-message">El nombre de usuario o el email ya están registrados.</p>';
            } else {
                // Insertar nuevo usuario
                $stmt_insertar = $conn->prepare("INSERT INTO user (nombre_usuario, email, contrasena) VALUES (?, ?, ?)");
                $stmt_insertar->bind_param("sss", $nombre_usuario, $email, $contrasena);

                if ($stmt_insertar->execute()) {
                    echo '<p class="success-message">Registro exitoso. <a href="login.php">Iniciar sesión</a></p>';
                } else {
                    echo '<p class="error-message">Error al registrar el usuario: ' . $conn->error . '</p>';
                }

                $stmt_insertar->close();
            }

            $stmt_verificar->close();
            $conn->close();
        }
        ?>
        <form method="post">
            <div class="form-group">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <div class="form-group">
                <button type="submit" name="registrar">Registrar</button>
            </div>
            <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 CromoGol tu tienda de cartas </p>
    </footer>
</body>
</html>