<?php
// cliente/cliente_dashboard.php
// NO vuelvas a iniciar sesión ni incluir otro header
include __DIR__ . '/../includes/header.php';
?>

<h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h2>
<p>Usa el menú de navegación para moverte por tu perfil y pedidos.</p>

<?php include __DIR__ . '/../includes/footer.php'; ?>
