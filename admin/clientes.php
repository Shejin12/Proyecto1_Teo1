<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'Administrador') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

// =======================
// ELIMINAR CLIENTE
// =======================
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM Usuario WHERE ID = ? AND Tipo='Cliente'");
    $stmt->execute([$id]);
    header("Location: clientes.php");
    exit;
}

// =======================
// OBTENER TODOS LOS CLIENTES
// =======================
$stmt = $pdo->query("SELECT * FROM Usuario WHERE Tipo='Cliente' ORDER BY NombreUsuario");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestionar Clientes</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Clientes</h1>
<a href="index.php">Volver al menú</a>

<!-- TABLA DE CLIENTES -->
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nombre de Usuario</th>
        <th>Contraseña</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($clientes as $c): ?>
    <tr>
        <td><?= $c['ID'] ?></td>
        <td><?= htmlspecialchars($c['NombreUsuario']) ?></td>
        <td><?= htmlspecialchars($c['Contrasena']) ?></td>
        <td>
            <a href="clientes.php?eliminar=<?= $c['ID'] ?>" onclick="return confirm('¿Eliminar este cliente?');">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>


</body>
</html>
