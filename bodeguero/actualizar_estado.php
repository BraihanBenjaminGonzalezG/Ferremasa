<?php
// /bodeguero/actualizar_estado.php
session_start();
include __DIR__ . '/../includes/db.php';

// Sólo rol bodeguero
if ($_SESSION['rol'] !== 'bodeguero') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedidoId   = (int) ($_POST['pedido_id'] ?? 0);
    $nuevoEstado= in_array($_POST['nuevo_estado'], ['en_preparacion','entregado'])
                  ? $_POST['nuevo_estado']
                  : 'aprobado';

    $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevoEstado, $pedidoId);
    $stmt->execute();
    $stmt->close();
}

header("Location: bodeguero_dashboard.php");
exit;
?>
