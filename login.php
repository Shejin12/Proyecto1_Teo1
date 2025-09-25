<?php
session_start();
require 'config/db.php';

$error_login = "";
$error_registro = "";

// ==========================
// LOGIN
// ==========================
if (isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE NombreUsuario = ? AND Contrasena = ?");
    $stmt->execute([$usuario, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['usuario_id'] = $user['ID'];
        $_SESSION['usuario_nombre'] = $user['NombreUsuario'];
        $_SESSION['usuario_tipo'] = $user['Tipo'];

        if ($user['Tipo'] === 'Administrador') {
            header("Location: admin/index.php");
        } else {
            header("Location: cliente/index.php");
        }
        exit;
    } else {
        $error_login = "Usuario o contraseña incorrectos.";
    }
}

// ==========================
// REGISTRO DE CLIENTE
// ==========================
if (isset($_POST['registro'])) {
    $nuevo_usuario = $_POST['nuevo_usuario'];
    $nueva_contrasena = $_POST['nueva_contrasena'];

    // Verificar si el nombre ya existe
    $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE NombreUsuario = ?");
    $stmt->execute([$nuevo_usuario]);
    if ($stmt->rowCount() > 0) {
        $error_registro = "El nombre de usuario ya existe.";
    } else {
        // Insertar solo como Cliente
        $stmt = $pdo->prepare("INSERT INTO Usuario (Tipo, NombreUsuario, Contrasena) VALUES ('Cliente', ?, ?)");
        $stmt->execute([$nuevo_usuario, $nueva_contrasena]);
        $error_registro = "Registro exitoso. Ya puedes iniciar sesión.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - Tienda</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* Formulario de registro inicialmente oculto */
        #registro-form {
            display: none;
            margin-top: 20px;
        }
        .toggle-btn {
            background: none;
            border: none;
            color: #2d7a2d;
            cursor: pointer;
            text-decoration: underline;
            margin-top: 10px;
        }
        .toggle-btn:hover {
            color: #145214;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>
    <?php if ($error_login) echo "<p class='error'>$error_login</p>"; ?>
    <form method="post">
        <label>Usuario:</label>
        <input type="text" name="usuario" required>
        <label>Contraseña:</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Ingresar</button>
    </form>

    <!-- Botón para mostrar el registro -->
    <button class="toggle-btn" onclick="mostrarRegistro()">¿Nuevo Usuario? Regístrate</button>

    <!-- FORMULARIO DE REGISTRO -->
    <div id="registro-form">
        <h2>Registrar Nuevo Cliente</h2>
        <?php if ($error_registro) echo "<p class='error'>$error_registro</p>"; ?>
        <form method="post">
            <label>Nombre de Usuario:</label>
            <input type="text" name="nuevo_usuario" required>
            <label>Contraseña:</label>
            <input type="password" name="nueva_contrasena" required>
            <button type="submit" name="registro">Registrar</button>
        </form>
    </div>
</div>

<script>
    function mostrarRegistro() {
        const form = document.getElementById('registro-form');
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
</script>

</body>
</html>
