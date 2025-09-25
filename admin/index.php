<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'Administrador') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Administrador</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Bienvenido Administrador, <?= $_SESSION['usuario_nombre'] ?></h1>
<nav>
    <a href="productos.php">Gestionar Productos</a> |
    <a href="clientes.php">Gestionar Clientes</a> |
    <a href="computadoras.php">Armar Computadoras</a> |
    <a href="ventas.php">Ver Ventas</a> |
    <a href="../logout.php">Cerrar sesión</a>
</nav>
</body>
</html>
