<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'Administrador') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

if (isset($_POST['agregar'])) {
    $ram = $_POST['ram'];
    $ssd = $_POST['ssd'];
    $fuente = $_POST['fuente'];
    $gabinete = $_POST['gabinete'];
    $procesador = $_POST['procesador'];

    $stmt = $pdo->prepare("SELECT Precio FROM Productos WHERE ID = ?");
    
    $stmt->execute([$ram]);
    $precio_ram = $stmt->fetchColumn();

    $stmt->execute([$ssd]);
    $precio_ssd = $stmt->fetchColumn();

    $stmt->execute([$fuente]);
    $precio_fuente = $stmt->fetchColumn();

    $stmt->execute([$gabinete]);
    $precio_gabinete = $stmt->fetchColumn();

    $stmt->execute([$procesador]);
    $precio_procesador = $stmt->fetchColumn();

    $precio_total = $precio_ram + $precio_ssd + $precio_fuente + $precio_gabinete + $precio_procesador;

    $stmt = $pdo->prepare("
        INSERT INTO Computadora
        (Nombre, ProductoRAM, ProductoSSD, ProductoFuente, ProductoGabinete, ProductoProcesador, Precio, Estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'En venta')
    ");

    $stmt->execute([
        $_POST['nombre'],
        $ram,
        $ssd,
        $fuente,
        $gabinete,
        $procesador,
        $precio_total
    ]);

    header("Location: computadoras.php");
    exit;
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM Computadora WHERE ID = ?");
    $stmt->execute([$id]);
    header("Location: computadoras.php");
    exit;
}

$productos_ram = $pdo->query("SELECT * FROM Productos WHERE Tipo='RAM'")->fetchAll(PDO::FETCH_ASSOC);
$productos_ssd = $pdo->query("SELECT * FROM Productos WHERE Tipo='SSD'")->fetchAll(PDO::FETCH_ASSOC);
$productos_fuente = $pdo->query("SELECT * FROM Productos WHERE Tipo='Fuente'")->fetchAll(PDO::FETCH_ASSOC);
$productos_gabinete = $pdo->query("SELECT * FROM Productos WHERE Tipo='Gabinete'")->fetchAll(PDO::FETCH_ASSOC);
$productos_procesador = $pdo->query("SELECT * FROM Productos WHERE Tipo='Procesador'")->fetchAll(PDO::FETCH_ASSOC);

$computadoras = $pdo->query("
    SELECT c.ID, c.Nombre,
           r.Nombre as RAM, 
           s.Nombre as SSD, 
           f.Nombre as Fuente, 
           g.Nombre as Gabinete, 
           p.Nombre as Procesador,
           c.Precio
    FROM Computadora c
    JOIN Productos r ON c.ProductoRAM = r.ID
    JOIN Productos s ON c.ProductoSSD = s.ID
    JOIN Productos f ON c.ProductoFuente = f.ID
    JOIN Productos g ON c.ProductoGabinete = g.ID
    JOIN Productos p ON c.ProductoProcesador = p.ID
    WHERE c.Estado = 'En venta'
    ORDER BY c.ID
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Armar Computadoras</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Computadoras Armadas</h1>
<a href="index.php">Volver al menú</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>RAM</th>
        <th>SSD</th>
        <th>Fuente</th>
        <th>Gabinete</th>
        <th>Procesador</th>
        <th>Precio</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($computadoras as $c): ?>
    <tr>
        <td><?= $c['ID'] ?></td>
        <td><?= htmlspecialchars($c['Nombre']) ?></td>
        <td><?= htmlspecialchars($c['RAM']) ?></td>
        <td><?= htmlspecialchars($c['SSD']) ?></td>
        <td><?= htmlspecialchars($c['Fuente']) ?></td>
        <td><?= htmlspecialchars($c['Gabinete']) ?></td>
        <td><?= htmlspecialchars($c['Procesador']) ?></td>
        <td><?= number_format($c['Precio'],2) ?></td>
        <td>
            <a href="computadoras.php?eliminar=<?= $c['ID'] ?>" onclick="return confirm('¿Eliminar esta computadora?');">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Agregar Nueva Computadora</h2>
<form method="post">
    <label>Nombre de la Computadora:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>RAM:</label><br>
    <select name="ram" required>
        <?php foreach($productos_ram as $p): ?>
        <option value="<?= $p['ID'] ?>"><?= htmlspecialchars($p['Nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>SSD:</label><br>
    <select name="ssd" required>
        <?php foreach($productos_ssd as $p): ?>
        <option value="<?= $p['ID'] ?>"><?= htmlspecialchars($p['Nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Fuente:</label><br>
    <select name="fuente" required>
        <?php foreach($productos_fuente as $p): ?>
        <option value="<?= $p['ID'] ?>"><?= htmlspecialchars($p['Nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Gabinete:</label><br>
    <select name="gabinete" required>
        <?php foreach($productos_gabinete as $p): ?>
        <option value="<?= $p['ID'] ?>"><?= htmlspecialchars($p['Nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Procesador:</label><br>
    <select name="procesador" required>
        <?php foreach($productos_procesador as $p): ?>
        <option value="<?= $p['ID'] ?>"><?= htmlspecialchars($p['Nombre']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit" name="agregar">Agregar Computadora</button>
</form>

</body>
</html>
