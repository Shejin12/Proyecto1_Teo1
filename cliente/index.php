<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'Cliente') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cliente</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Bienvenido <?= $_SESSION['usuario_nombre'] ?></h1>
<nav>
    <a href="tienda.php">Ver Productos</a> |
    <a href="carrito.php">Ver Carrito</a> |
    <a href="../logout.php">Cerrar sesión</a>
</nav>
</body>
</html>
