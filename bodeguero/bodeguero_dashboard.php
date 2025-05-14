<?php
// /bodeguero/bodeguero_dashboard.php
session_start();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/db.php';

// Sólo rol bodeguero
if ($_SESSION['rol'] !== 'bodeguero') {
    header("Location: ../login.php");
    exit;
}

// 1) Pedidos aprobados (lista para preparación)
$stmt1 = $conn->prepare("
  SELECT p.id, u.nombre AS cliente, p.fecha, p.tipo_entrega 
  FROM pedidos p
  JOIN usuarios u ON p.usuario_id = u.id
  WHERE p.estado = 'aprobado'
  ORDER BY p.fecha DESC
");
$stmt1->execute();
$aprobados = $stmt1->get_result();
$stmt1->close();

// 2) Pedidos en preparación (lista para entregar)
$stmt2 = $conn->prepare("
  SELECT p.id, u.nombre AS cliente, p.fecha, p.tipo_entrega 
  FROM pedidos p
  JOIN usuarios u ON p.usuario_id = u.id
  WHERE p.estado = 'en_preparacion'
  ORDER BY p.fecha DESC
");
$stmt2->execute();
$enprep = $stmt2->get_result();
$stmt2->close();
?>

<h2>Panel de Bodeguero</h2>

<section>
  <h3>Pedidos Aprobados (Listos para preparar)</h3>
  <?php if ($aprobados->num_rows === 0): ?>
    <p>No hay pedidos aprobados.</p>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <thead>
        <tr>
          <th>ID</th><th>Cliente</th><th>Fecha</th><th>Entrega</th><th>Acción</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $aprobados->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['cliente']); ?></td>
          <td><?php echo $row['fecha']; ?></td>
          <td><?php echo ucfirst($row['tipo_entrega']); ?></td>
          <td>
            <form method="POST" action="actualizar_estado.php" style="display:inline">
              <input type="hidden" name="pedido_id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="nuevo_estado" value="en_preparacion">
              <button type="submit">Marcar en preparación</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<section>
  <h3>Pedidos en Preparación (Listos para entrega)</h3>
  <?php if ($enprep->num_rows === 0): ?>
    <p>No hay pedidos en preparación.</p>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <thead>
        <tr>
          <th>ID</th><th>Cliente</th><th>Fecha</th><th>Entrega</th><th>Acción</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $enprep->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['cliente']); ?></td>
          <td><?php echo $row['fecha']; ?></td>
          <td><?php echo ucfirst($row['tipo_entrega']); ?></td>
          <td>
            <form method="POST" action="actualizar_estado.php" style="display:inline">
              <input type="hidden" name="pedido_id" value="<?php echo $row['id']; ?>">
              <input type="hidden" name="nuevo_estado" value="entregado">
              <button type="submit">Marcar entregado</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
