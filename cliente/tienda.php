<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'Cliente') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (isset($_POST['agregar_producto'])) {
    $producto_id = $_POST['producto_id'];
    $_SESSION['carrito'][] = ['tipo'=>'producto', 'id'=>$producto_id];
    header("Location: tienda.php");
    exit;
}

if (isset($_POST['agregar_computadora'])) {
    $computadora_id = $_POST['computadora_id'];
    $_SESSION['carrito'][] = ['tipo'=>'computadora', 'id'=>$computadora_id];
    header("Location: tienda.php");
    exit;
}

$productos = $pdo->query("SELECT * FROM Productos ORDER BY Tipo, Nombre")->fetchAll(PDO::FETCH_ASSOC);

$computadoras = $pdo->query("
    SELECT c.ID, c.Nombre, c.Precio,
           r.Nombre AS RAM, s.Nombre AS SSD, f.Nombre AS Fuente, g.Nombre AS Gabinete, p.Nombre AS Procesador
    FROM Computadora c
    JOIN Productos r ON c.ProductoRAM = r.ID
    JOIN Productos s ON c.ProductoSSD = s.ID
    JOIN Productos f ON c.ProductoFuente = f.ID
    JOIN Productos g ON c.ProductoGabinete = g.ID
    JOIN Productos p ON c.ProductoProcesador = p.ID
    ORDER BY c.ID
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tienda</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h1>
<a href="carrito.php">Ver Carrito (<?= count($_SESSION['carrito']) ?>)</a> |
<a href="index.php">Volver al menú</a>

<h2>Productos Disponibles</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Precio</th>
        <th>Acción</th>
    </tr>
    <?php foreach($productos as $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['Nombre']) ?></td>
        <td><?= htmlspecialchars($p['Tipo']) ?></td>
        <td><?= number_format($p['Precio'], 2) ?></td>
        <td>
            <form method="post">
                <input type="hidden" name="producto_id" value="<?= $p['ID'] ?>">
                <button type="submit" name="agregar_producto">Agregar al Carrito</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Computadoras Armadas</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Nombre</th>
        <th>RAM</th>
        <th>SSD</th>
        <th>Fuente</th>
        <th>Gabinete</th>
        <th>Procesador</th>
        <th>Precio</th>
        <th>Acción</th>
    </tr>
    <?php foreach($computadoras as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['Nombre']) ?></td>
        <td><?= htmlspecialchars($c['RAM']) ?></td>
        <td><?= htmlspecialchars($c['SSD']) ?></td>
        <td><?= htmlspecialchars($c['Fuente']) ?></td>
        <td><?= htmlspecialchars($c['Gabinete']) ?></td>
        <td><?= htmlspecialchars($c['Procesador']) ?></td>
        <td><?= number_format($c['Precio'], 2) ?></td>
        <td>
            <form method="post">
                <input type="hidden" name="computadora_id" value="<?= $c['ID'] ?>">
                <button type="submit" name="agregar_computadora">Agregar al Carrito</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
