<?php
// /vendedor/vendedor_dashboard.php
session_start();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/db.php';

// Sólo rol vendedor
if ($_SESSION['rol'] !== 'vendedor') {
    header("Location: ../login.php");
    exit;
}

// 1) Obtener pedidos pendientes
$sql = "
  SELECT p.id, p.usuario_id, u.nombre AS cliente, p.fecha, p.tipo_entrega 
  FROM pedidos p
  JOIN usuarios u ON p.usuario_id = u.id
  WHERE p.estado = 'pendiente'
  ORDER BY p.fecha DESC
";
$pedidos = $conn->query($sql);

// 2) Obtener stock de productos
$sql2 = "SELECT id, nombre, stock FROM productos ORDER BY nombre";
$productos = $conn->query($sql2);
?>

<h2>Panel de Vendedor</h2>

<section>
  <h3>Pedidos Pendientes</h3>
  <?php if ($pedidos->num_rows === 0): ?>
    <p>No hay pedidos pendientes.</p>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Fecha</th>
          <th>Entrega</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $pedidos->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['cliente']); ?></td>
          <td><?php echo $row['fecha']; ?></td>
          <td><?php echo ucfirst($row['tipo_entrega']); ?></td>
          <td>
            <form style="display:inline" method="POST" action="actualizar_pedido.php">
              <input type="hidden" name="pedido_id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="estado" value="aprobado">
              <button type="submit">Aprobar</button>
            </form>
            <form style="display:inline" method="POST" action="actualizar_pedido.php">
              <input type="hidden" name="pedido_id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="estado" value="rechazado">
              <button type="submit">Rechazar</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<section>
  <h3>Stock de Productos</h3>
  <?php if ($productos->num_rows === 0): ?>
    <p>No hay productos registrados.</p>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <thead>
        <tr>
          <th>ID</th>
          <th>Producto</th>
          <th>Stock</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($p = $productos->fetch_assoc()): ?>
        <tr>
          <td><?php echo $p['id']; ?></td>
          <td><?php echo htmlspecialchars($p['nombre']); ?></td>
          <td>
            <!-- Formulario inline para actualizar stock -->
            <form method="POST" action="actualizar_stock.php" style="display:flex; align-items:center;">
              <input type="hidden" name="producto_id" value="<?php echo $p['id']; ?>">
              <input 
                type="number" 
                name="stock" 
                value="<?php echo $p['stock']; ?>" 
                min="0" 
                style="width:60px; margin-right:0.5rem;"
              >
              <button type="submit">Actualizar</button>
            </form>
          </td>
          <td><!-- vacía, o aquí podrías añadir enlaces a detalle -->
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>


<?php include __DIR__ . '/../includes/footer.php'; ?>
