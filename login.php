<?php
session_start();
include "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $clave  = $_POST['clave'];

    // Ahora traemos también el nombre
    $stmt = $conn->prepare(
      "SELECT id, nombre, clave, rol 
       FROM usuarios 
       WHERE correo = ? AND estado = 1"
    );
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $nombre, $hash, $rol);
        $stmt->fetch();

        if (password_verify($clave, $hash)) {
            // Guardamos nombre y id en sesión
            $_SESSION['usuario_id']     = $id;
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['rol']            = $rol;

            header("Location: {$rol}/{$rol}_dashboard.php");
            exit;
        } else {
            echo "Clave incorrecta";
        }
    } else {
        echo "Usuario no encontrado o inactivo";
    }
}
?>








<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
</head>
<body>
  <h2>Iniciar Sesión</h2>
  <form method="POST" action="login.php">
    <input type="email" name="correo" placeholder="Correo electrónico" required><br><br>
    <input type="password" name="clave" placeholder="Contraseña" required><br><br>
    <button type="submit">Ingresar</button>
  </form>

  <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
</body>
</html>
