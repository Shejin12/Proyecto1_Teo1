<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'Cliente') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// =======================
// ELIMINAR ITEM DEL CARRITO
// =======================
if (isset($_GET['eliminar'])) {
    $index = $_GET['eliminar'];
    if (isset($_SESSION['carrito'][$index])) {
        unset($_SESSION['carrito'][$index]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']); // reindexar
    }
    header("Location: carrito.php");
    exit;
}

// =======================
// VERIFICAR SI SE PUEDE COMPRAR
// =======================
$tipos_necesarios = ['RAM','SSD','Fuente','Gabinete','Procesador'];
$tipos_en_carrito = [];
$total_precio = 0;

// Obtener info de los productos
$productos_carrito = [];
foreach($_SESSION['carrito'] as $item) {
    $stmt = $pdo->prepare("SELECT * FROM Productos WHERE ID=?");
    $stmt->execute([$item['id']]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($prod) {
        $productos_carrito[] = $prod;
        $tipos_en_carrito[$prod['Tipo']] = true;
        $total_precio += $prod['Precio'];
    }
}

$puede_comprar = true;
foreach($tipos_necesarios as $t){
    if(!isset($tipos_en_carrito[$t])) $puede_comprar = false;
}

// =======================
// COMPRAR
// =======================
if(isset($_POST['comprar']) && $puede_comprar) {
    // Crear computadora nueva
    $componentes = [];
    foreach($productos_carrito as $p) {
        $componentes[$p['Tipo']] = $p['ID'];
    }

    $stmt = $pdo->prepare("
        INSERT INTO Computadora (Nombre, ProductoRAM, ProductoSSD, ProductoFuente, ProductoGabinete, ProductoProcesador, Precio, Estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Vendido')
    ");

    $nombre = "PC personalizada #" . time();
    $stmt->execute([
        $nombre,
        $componentes['RAM'],
        $componentes['SSD'],
        $componentes['Fuente'],
        $componentes['Gabinete'],
        $componentes['Procesador'],
        $total_precio
    ]);

    $id_compu = $pdo->lastInsertId();

    // Reducir cantidades de los productos
    foreach($productos_carrito as $p){
        $stmt = $pdo->prepare("UPDATE Productos SET CantidadDisponible = CantidadDisponible - 1 WHERE ID=? AND CantidadDisponible>0");
        $stmt->execute([$p['ID']]);
    }

    // Registrar venta
    $stmt = $pdo->prepare("INSERT INTO Venta (IDCliente, IDCompu, Precio, Estado) VALUES (?, ?, ?, 'En Proceso')");
    $stmt->execute([$_SESSION['usuario_id'], $id_compu, $total_precio]);

    // Limpiar carrito
    $_SESSION['carrito'] = [];
    header("Location: carrito.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Carrito</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Carrito de Compras</h1>
<a href="tienda.php">Seguir Comprando</a> |
<a href="index.php">Volver al menú</a>

<?php if(empty($_SESSION['carrito'])): ?>
    <p>Tu carrito está vacío.</p>
<?php else: ?>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Tipo</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Acción</th>
    </tr>
    <?php foreach($productos_carrito as $index => $p): ?>
    <tr>
        <td><?= htmlspecialchars($p['Tipo']) ?></td>
        <td><?= htmlspecialchars($p['Nombre']) ?></td>
        <td><?= number_format($p['Precio'],2) ?></td>
        <td><a href="?eliminar=<?= $index ?>" onclick="return confirm('¿Eliminar este item del carrito?');">Eliminar</a></td>
    </tr>
    <?php endforeach; ?>
</table>

<p><strong>Total: $<?= number_format($total_precio,2) ?></strong></p>

<?php if($puede_comprar): ?>
<form method="post">
    <button type="submit" name="comprar">Comprar y Crear Computadora</button>
</form>
<?php else: ?>
<p style="color:red;">Debes agregar al menos un producto de cada tipo (RAM, SSD, Fuente, Gabinete, Procesador) para poder comprar.</p>
<?php endif; ?>

<?php endif; ?>
</body>
</html>
