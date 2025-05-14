<?php
// /vendedor/actualizar_pedido.php
session_start();
include __DIR__ . '/../includes/db.php';

// Sólo rol vendedor
if ($_SESSION['rol'] !== 'vendedor') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedidoId = (int) ($_POST['pedido_id'] ?? 0);
    $nuevoEstado = $_POST['estado'] === 'aprobado' ? 'aprobado' : 'rechazado';

    $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevoEstado, $pedidoId);
    $stmt->execute();
}

header("Location: vendedor_dashboard.php");
exit;
