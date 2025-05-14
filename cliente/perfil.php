<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['usuario_id'];
// Obtenemos datos del usuario
$stmt = $conn->prepare("SELECT nombre, correo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre, $correo);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Perfil</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
  <?php include "../includes/header.php"; ?>

  <h2>Mi Perfil</h2>
  <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
  <p><strong>Correo:</strong> <?php echo htmlspecialchars($correo); ?></p>
  <p><a href="cliente_dashboard.php">← Volver al panel</a></p>

  <?php include "../includes/footer.php"; ?>
</body>
</html>
