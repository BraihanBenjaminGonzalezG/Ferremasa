<?php
session_start();
include __DIR__ . '/../includes/db.php';

// Recogemos datos del formulario
if (isset($_POST['producto_id'], $_POST['cantidad'])) {
    $prodId  = (int) $_POST['producto_id'];
    $cantidad= (int) $_POST['cantidad'];

    // Inicializamos el carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Sumamos cantidad si ya estaba, o lo agregamos
    if (isset($_SESSION['carrito'][$prodId])) {
        $_SESSION['carrito'][$prodId] += $cantidad;
    } else {
        $_SESSION['carrito'][$prodId] = $cantidad;
    }
}

// Redirigimos de vuelta al catálogo (o donde prefieras)
header("Location: /Ferremasa/catalogo.php");
exit;
?>
