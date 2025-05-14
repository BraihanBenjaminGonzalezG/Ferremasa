<?php
// cliente/historial_pedidos.php
session_start();
include "../includes/db.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: ../login.php");
    exit;
}

$id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("
  SELECT p.id, p.fecha, p.estado, p.tipo_entrega 
  FROM pedidos p 
  WHERE p.usuario_id = ? 
  ORDER BY p.fecha DESC
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Pedidos</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <style>
    .estado-pendiente  { color: #b58900; font-weight: bold; }
    .estado-aprobado   { color: #2aa198; font-weight: bold; }
    .estado-rechazado  { color: #dc322f; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; border-bottom: 1px solid #ccc; text-align: left; }
  </style>
</head>
<body>
  <?php include "../includes/header.php"; ?>

  <h2>Mis Pedidos</h2>
  <?php if ($result->num_rows === 0): ?>
    <p>No tienes pedidos aún.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID Pedido</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th>Entrega</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): 
            // Definir clase CSS según estado
            $cls = match($row['estado']) {
              'pendiente' => 'estado-pendiente',
              'aprobado'  => 'estado-aprobado',
              'rechazado' => 'estado-rechazado',
              default     => ''
            };
        ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['fecha']; ?></td>
            <td class="<?php echo $cls; ?>">
              <?php echo ucfirst($row['estado']); ?>
            </td>
            <td><?php echo ucfirst($row['tipo_entrega']); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <?php include "../includes/footer.php"; ?>
</body>
</html>
<?php $stmt->close(); ?>
