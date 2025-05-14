<?php
// cliente/confirmacion.php
session_start();
include __DIR__ . '/../includes/header.php';

$pedidoId = isset($_GET['pedido']) ? (int)$_GET['pedido'] : 0;
?>
<h2>¡Pedido enviado!</h2>
<?php if ($pedidoId): ?>
  <p>Tu pedido <strong>#<?php echo $pedidoId; ?></strong> se ha creado correctamente.</p>
  <p>Puedes seguir su estado en <a href="historial_pedidos.php">Mis pedidos</a>.</p>
<?php else: ?>
  <p>No se pudo procesar tu pedido. <a href="/Ferremasa/catalogo.php">Volver al catálogo</a></p>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
