<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'Administrador') {
    header("Location: ../login.php");
    exit;
}

require '../config/db.php';

if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    $stmt = $pdo->prepare("INSERT INTO Productos (Nombre, Tipo, Descripcion, CantidadDisponible, Precio) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $tipo, $descripcion, $cantidad, $precio]);
    header("Location: productos.php");
    exit;
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM Productos WHERE ID = ?");
    $stmt->execute([$id]);
    header("Location: productos.php");
    exit;
}

if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    $stmt = $pdo->prepare("UPDATE Productos SET Nombre = ?, Tipo = ?, Descripcion = ?, CantidadDisponible = ?, Precio = ? WHERE ID = ?");
    $stmt->execute([$nombre, $tipo, $descripcion, $cantidad, $precio, $id]);
    header("Location: productos.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM Productos ORDER BY Tipo, Nombre");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestionar Productos</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<h1>Productos</h1>
<a href="index.php">Volver al menú</a>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Descripción</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($productos as $p): ?>
    <tr>
        <form method="post">
            <td><?= $p['ID'] ?><input type="hidden" name="id" value="<?= $p['ID'] ?>"></td>
            <td><input type="text" name="nombre" value="<?= htmlspecialchars($p['Nombre']) ?>" required></td>
            <td>
                <select name="tipo" required>
                    <option value="RAM" <?= $p['Tipo']=='RAM'?'selected':'' ?>>RAM</option>
                    <option value="SSD" <?= $p['Tipo']=='SSD'?'selected':'' ?>>SSD</option>
                    <option value="Fuente" <?= $p['Tipo']=='Fuente'?'selected':'' ?>>Fuente</option>
                    <option value="Gabinete" <?= $p['Tipo']=='Gabinete'?'selected':'' ?>>Gabinete</option>
                    <option value="Procesador" <?= $p['Tipo']=='Procesador'?'selected':'' ?>>Procesador</option>
                </select>
            </td>
            <td><input type="text" name="descripcion" value="<?= htmlspecialchars($p['Descripcion']) ?>"></td>
            <td><input type="number" name="cantidad" value="<?= $p['CantidadDisponible'] ?>" min="0" required></td>
            <td><input type="number" name="precio" value="<?= $p['Precio'] ?>" step="0.01" min="0" required></td>
            <td>
                <button type="submit" name="editar">Guardar</button>
                <a href="productos.php?eliminar=<?= $p['ID'] ?>" onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
            </td>
        </form>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Agregar Producto Nuevo</h2>
<form method="post">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br>
    <label>Tipo:</label><br>
    <select name="tipo" required>
        <option value="RAM">RAM</option>
        <option value="SSD">SSD</option>
        <option value="Fuente">Fuente</option>
        <option value="Gabinete">Gabinete</option>
        <option value="Procesador">Procesador</option>
    </select><br>
    <label>Descripción:</label><br>
    <textarea name="descripcion"></textarea><br>
    <label>Cantidad Disponible:</label><br>
    <input type="number" name="cantidad" min="0" required><br>
    <label>Precio:</label><br>
    <input type="number" name="precio" step="0.01" min="0" required><br><br>
    <button type="submit" name="agregar">Agregar Producto</button>
</form>

</body>
</html>
