<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'Administrador') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

// =======================
// CAMBIAR ESTADO DE VENTA
// =======================
if (isset($_POST['cambiar_estado'])) {
    $id_cliente = $_POST['cliente_id'];
    $id_compu = $_POST['compu_id'];
    $nuevo_estado = $_POST['estado'];

    if ($id_compu) {
        $stmt = $pdo->prepare("UPDATE Venta SET Estado = ? WHERE IDCliente = ? AND IDCompu = ?");
        $stmt->execute([$nuevo_estado, $id_cliente, $id_compu]);
    } else {
        // Producto individual: identificar por cliente y producto
        $stmt = $pdo->prepare("UPDATE Venta SET Estado = ? WHERE IDCliente = ? AND IDCompu IS NULL LIMIT 1");
        $stmt->execute([$nuevo_estado, $id_cliente]);
    }

    header("Location: ventas.php");
    exit;
}

// =======================
// OBTENER TODAS LAS VENTAS COMO COMPUTADORAS
// =======================
$ventas = [];

// 1️⃣ Computadoras reales
$compus = $pdo->query("
    SELECT v.IDCliente, u.NombreUsuario, v.IDCompu, v.Precio, v.Estado,
           c.Nombre AS Computadora,
           r.Nombre AS RAM, s.Nombre AS SSD, f.Nombre AS Fuente, g.Nombre AS Gabinete, p.Nombre AS Procesador
    FROM Venta v
    JOIN Usuario u ON v.IDCliente = u.ID
    JOIN Computadora c ON v.IDCompu = c.ID
    JOIN Productos r ON c.ProductoRAM = r.ID
    JOIN Productos s ON c.ProductoSSD = s.ID
    JOIN Productos f ON c.ProductoFuente = f.ID
    JOIN Productos g ON c.ProductoGabinete = g.ID
    JOIN Productos p ON c.ProductoProcesador = p.ID
")->fetchAll(PDO::FETCH_ASSOC);

$ventas = array_merge($ventas, $compus);

// 2️⃣ Productos individuales
$productos_indiv = $pdo->query("
    SELECT v.IDCliente, u.NombreUsuario, v.IDCompu, v.Precio, v.Estado,
           p.Nombre AS Producto, p.Tipo
    FROM Venta v
    JOIN Usuario u ON v.IDCliente = u.ID
    JOIN Productos p ON v.IDCompu IS NULL AND v.Precio = p.Precio
")->fetchAll(PDO::FETCH_ASSOC);

// Transformar productos individuales en “computadora virtual”
foreach ($productos_indiv as $p) {
    $ventas[] = [
        'IDCliente' => $p['IDCliente'],
        'NombreUsuario' => $p['NombreUsuario'],
        'IDCompu' => null,
        'Computadora' => 'Producto Individual',
        'RAM' => $p['Tipo'] == 'RAM' ? $p['Producto'] ?? $p['Producto'] : '',
        'SSD' => $p['Tipo'] == 'SSD' ? $p['Producto'] ?? $p['Producto'] : '',
        'Fuente' => $p['Tipo'] == 'Fuente' ? $p['Producto'] ?? $p['Producto'] : '',
        'Gabinete' => $p['Tipo'] == 'Gabinete' ? $p['Producto'] ?? $p['Producto'] : '',
        'Procesador' => $p['Tipo'] == 'Procesador' ? $p['Producto'] ?? $p['Producto'] : '',
        'Precio' => $p['Precio'],
        'Estado' => $p['Estado']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Ventas</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Ventas</h1>
<a href="index.php">Volver al menú</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Cliente</th>
        <th>Computadora / Producto</th>
        <th>RAM</th>
        <th>SSD</th>
        <th>Fuente</th>
        <th>Gabinete</th>
        <th>Procesador</th>
        <th>Precio</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($ventas as $v): ?>
    <tr>
        <td><?= htmlspecialchars($v['NombreUsuario']) ?></td>
        <td><?= htmlspecialchars($v['Computadora']) ?></td>
        <td><?= htmlspecialchars($v['RAM'] ?? '') ?></td>
        <td><?= htmlspecialchars($v['SSD'] ?? '') ?></td>
        <td><?= htmlspecialchars($v['Fuente'] ?? '') ?></td>
        <td><?= htmlspecialchars($v['Gabinete'] ?? '') ?></td>
        <td><?= htmlspecialchars($v['Procesador'] ?? '') ?></td>
        <td><?= number_format($v['Precio'], 2) ?></td>
        <td><?= $v['Estado'] ?></td>
        <td>
            <form method="post" style="display:inline;">
                <input type="hidden" name="cliente_id" value="<?= $v['IDCliente'] ?>">
                <input type="hidden" name="compu_id" value="<?= $v['IDCompu'] ?>">
                <select name="estado" required>
                    <option value="En Proceso" <?= $v['Estado']=='En Proceso'?'selected':'' ?>>En Proceso</option>
                    <option value="Ensamblado" <?= $v['Estado']=='Ensamblado'?'selected':'' ?>>Ensamblado</option>
                    <option value="Enviado" <?= $v['Estado']=='Enviado'?'selected':'' ?>>Enviado</option>
                    <option value="Finalizado" <?= $v['Estado']=='Finalizado'?'selected':'' ?>>Finalizado</option>
                </select>
                <button type="submit" name="cambiar_estado">Actualizar</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
