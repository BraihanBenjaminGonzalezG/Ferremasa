<?php
// carrito/ver_carrito.php
session_start();
include __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

// Cargamos el carrito de la sesión
$carrito = $_SESSION['carrito'] ?? [];

// Si está vacío, mostramos mensaje y salimos
if (empty($carrito)): ?>
  <p>Tu carrito está vacío. <a href="/Ferremasa/catalogo.php">Ir al catálogo</a></p>

<?php else: 
    // Preparamos la consulta dinámica
    $ids = array_keys($carrito);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT id, nombre, precio FROM productos WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($ids));
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
?>

  <h2>Mi Carrito</h2>
  <table>
    <thead>
      <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
    <?php 
      $total = 0;
      while ($row = $result->fetch_assoc()):
        $pid  = $row['id'];
        $cant = $carrito[$pid];
        $sub  = $row['precio'] * $cant;
        $total += $sub;
    ?>
      <tr>
        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
        <td>$<?php echo number_format($row['precio'], 0, ',', '.'); ?></td>
        <td><?php echo $cant; ?></td>
        <td>$<?php echo number_format($sub, 0, ',', '.'); ?></td>
      </tr>
    <?php endwhile; ?>
      <tr>
        <td colspan="3"><strong>Total</strong></td>
        <td><strong>$<?php echo number_format($total, 0, ',', '.'); ?></strong></td>
      </tr>
    </tbody>
  </table>

  <p>
    <a href="/Ferremasa/catalogo.php">Seguir comprando</a> |
    <a href="/Ferremasa/carrito/procesar_pedido.php">Finalizar compra</a>
  </p>

<?php
endif;
include __DIR__ . '/../includes/footer.php';
?>
