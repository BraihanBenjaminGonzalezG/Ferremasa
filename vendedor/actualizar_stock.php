<?php
// /vendedor/actualizar_stock.php
session_start();
include __DIR__ . '/../includes/db.php';

// Sólo rol vendedor
if ($_SESSION['rol'] !== 'vendedor') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prodId = isset($_POST['producto_id']) ? (int) $_POST['producto_id'] : 0;
    $newStock = isset($_POST['stock']) ? (int) $_POST['stock'] : 0;

    // Validar valores mínimos
    if ($prodId > 0 && $newStock >= 0) {
        $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $newStock, $prodId);
        $stmt->execute();
    }
}

// Volver al panel de vendedor
header("Location: vendedor_dashboard.php");
exit;
?>
