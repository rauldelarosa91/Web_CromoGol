<?php
session_start();
session_destroy();
header("Location: index.php"); // Redirigir a la página principal después de cerrar sesión
exit();
?>