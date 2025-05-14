<?php
// /contador/contador_dashboard.php
session_start();
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/db.php';

// Solo rol contador
if ($_SESSION['rol'] !== 'contador') {
    header("Location: ../login.php");
    exit;
}

// 1) Obtener pagos pendientes
$sql1 = "
  SELECT pay.id AS pago_id,
         pay.pedido_id,
         pay.metodo,
         pay.fecha,
         u.nombre AS cliente
  FROM pagos pay
  JOIN pedidos p ON pay.pedido_id = p.id
  JOIN usuarios u ON p.usuario_id = u.id
  WHERE pay.estado = 'pendiente'
  ORDER BY pay.fecha DESC
";
$pendientes = $conn->query($sql1);

// 2) Obtener pagos confirmados (histórico)
$sql2 = "
  SELECT pay.id AS pago_id,
         pay.pedido_id,
         pay.metodo,
         pay.fecha,
         u.nombre AS cliente
  FROM pagos pay
  JOIN pedidos p ON pay.pedido_id = p.id
  JOIN usuarios u ON p.usuario_id = u.id
  WHERE pay.estado = 'confirmado'
  ORDER BY pay.fecha DESC
";
$confirmados = $conn->query($sql2);

// 3) Reporte Financiero: total general confirmado
$sqlTotal = "
  SELECT SUM(dp.subtotal) AS total_confirmado
  FROM pagos pay
  JOIN detalle_pedido dp ON dp.pedido_id = pay.pedido_id
  WHERE pay.estado = 'confirmado'
";
$rowTotal = $conn->query($sqlTotal)->fetch_assoc();
$totalConfirmado = $rowTotal['total_confirmado'] ?? 0;

// 4) Reporte Financiero: desglose mensual
$sqlMeses = "
  SELECT 
    YEAR(pay.fecha) AS anio, 
    MONTH(pay.fecha) AS mes, 
    SUM(dp.subtotal) AS total
  FROM pagos pay
  JOIN detalle_pedido dp ON dp.pedido_id = pay.pedido_id
  WHERE pay.estado = 'confirmado'
  GROUP BY anio, mes
  ORDER BY anio DESC, mes DESC
";
$reporteMeses = $conn->query($sqlMeses);
?>

<h2>Panel de Contador</h2>

<section>
  <h3>Pagos Pendientes</h3>
  <?php if ($pendientes->num_rows === 0): ?>
    <p>No tienes pagos pendientes.</p>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <thead>
        <tr>
          <th>ID Pago</th><th>Pedido</th><th>Cliente</th><th>Método</th><th>Fecha</th><th>Acción</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $pendientes->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['pago_id']; ?></td>
          <td><?php echo $row['pedido_id']; ?></td>
          <td><?php echo htmlspecialchars($row['cliente']); ?></td>
          <td><?php echo htmlspecialchars($row['metodo']); ?></td>
          <td><?php echo $row['fecha']; ?></td>
          <td>
            <form method="POST" action="actualizar_pago.php" style="display:inline;">
              <input type="hidden" name="pago_id" value="<?php echo $row['pago_id']; ?>">
              <button type="submit">Confirmar Pago</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<section>
  <h3>Histórico de Pagos Confirmados</h3>
  <?php if ($confirmados->num_rows === 0): ?>
    <p>No hay pagos confirmados aún.</p>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <thead>
        <tr>
          <th>ID Pago</th><th>Pedido</th><th>Cliente</th><th>Método</th><th>Fecha</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $confirmados->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['pago_id']; ?></td>
          <td><?php echo $row['pedido_id']; ?></td>
          <td><?php echo htmlspecialchars($row['cliente']); ?></td>
          <td><?php echo htmlspecialchars($row['metodo']); ?></td>
          <td><?php echo $row['fecha']; ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<section>
  <h3>Reporte Financiero</h3>
  <p><strong>Total Confirmado:</strong> $
    <?php echo number_format($totalConfirmado, 0, ',', '.'); ?>
  </p>

  <?php if ($reporteMeses->num_rows > 0): ?>
    <table border="1" cellpadding="6">
      <thead>
        <tr>
          <th>Año</th>
          <th>Mes</th>
          <th>Total Pagado</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($r = $reporteMeses->fetch_assoc()): ?>
          <tr>
            <td><?php echo $r['anio']; ?></td>
            <td><?php echo $r['mes']; ?></td>
            <td>$
              <?php echo number_format($r['total'], 0, ',', '.'); ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No hay datos para el reporte financiero.</p>
  <?php endif; ?>
   <!-- Botón para exportar -->
  <form method="GET" action="/Ferremasa/contador/exportar_reporte.php" style="margin-top:1rem;">
    <button type="submit">📥 Exportar a Excel</button>
  </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
