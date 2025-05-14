<?php
// /contador/actualizar_pago.php
session_start();
include __DIR__ . '/../includes/db.php';

// Solo rol contador
if ($_SESSION['rol'] !== 'contador') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pagoId = (int) ($_POST['pago_id'] ?? 0);

    // 1) Actualizar estado del pago
    $stmt = $conn->prepare("UPDATE pagos SET estado = 'confirmado' WHERE id = ?");
    $stmt->bind_param("i", $pagoId);
    $stmt->execute();
    $stmt->close();

    // 2) (Opcional) cambiar estado del pedido a 'pagado'
    $conn->query("
      UPDATE pedidos 
      SET estado = 'pagado' 
      WHERE id = (
        SELECT pedido_id FROM pagos WHERE id = $pagoId
      )
    ");
}

header("Location: contador_dashboard.php");
exit;
?>
