<?php
// includes/header.php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}
$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>FERREMASA</title>
  <link rel="stylesheet" href="/FERREMASA/assets/css/styles.css">
</head>
<body>
  <header>
    <nav class="main-nav">
      <ul>
        <li><a href="/FERREMASA/index.php">Inicio</a></li>
        <?php if ($rol === 'cliente'): ?>
          <li><a href="/FERREMASA/catalogo.php">Catálogo</a></li>
          <li><a href="/FERREMASA/cliente/perfil.php">Mi perfil</a></li>
          <li><a href="/FERREMASA/cliente/historial_pedidos.php">Mis pedidos</a></li>
          <li><a href="/Ferremasa/carrito/ver_carrito.php">Carrito</a></li> 
        <?php endif; ?>
        <?php if ($rol === 'admin'): /* … otras opciones …*/ endif; ?>
        <?php if (in_array($rol, ['vendedor','bodeguero','contador'])): ?>
          <?php if ($rol === 'vendedor'): ?>
            <!-- vendedor links -->
          <?php elseif ($rol === 'bodeguero'): ?>
            <!-- bodeguero links -->
          <?php elseif ($rol === 'contador'): ?>
            <li><a href="/Ferremasa/contador/contador_dashboard.php">Contador</a></li>
          <?php endif; ?>
        <?php endif; ?>

      </ul>
      <div class="user-menu">
        Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> |
        <a href="/FERREMASA/logout.php">Cerrar sesión</a>
      </div>
    </nav>
  </header>
  <main>

