<?php
include "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $clave = $_POST['clave'];
    
    // Validar si el correo ya existe
    $check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $check->bind_param("s", $correo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "❌ El correo ya está registrado.";
    } else {
        // Encriptar la contraseña
        $claveHash = password_hash($clave, PASSWORD_DEFAULT);

        // Insertar en la base de datos
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, clave, rol) VALUES (?, ?, ?, 'cliente')");
        $stmt->bind_param("sss", $nombre, $correo, $claveHash);
        
        if ($stmt->execute()) {
            echo "✅ Registro exitoso. Puedes <a href='login.php'>iniciar sesión</a>.";
        } else {
            echo "❌ Error al registrar: " . $conn->error;
        }
    }
}
?>




<!-- register.php -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Cliente</title>
</head>
<body>
  <h2>Registro de Cliente</h2>
  <form action="register.php" method="POST">
    <input type="text" name="nombre" placeholder="Nombre completo" required><br><br>
    <input type="email" name="correo" placeholder="Correo electrónico" required><br><br>
    <input type="password" name="clave" placeholder="Contraseña" required><br><br>
    <button type="submit">Registrarse</button>
  </form>

  <p>¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>
