<?php
// carrito/procesar_pedido.php
session_start();
include __DIR__ . '/../includes/db.php';

$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    header("Location: /Ferremasa/carrito/ver_carrito.php");
    exit;
}

$userId = $_SESSION['usuario_id'];
$fecha   = date('Y-m-d H:i:s');
// Para un checkout completo podrías pasar tipo_entrega y dirección desde un formulario.
// De momento los dejamos por defecto:
$tipoEntrega = 'retiro';
$direccion   = '';

$conn->begin_transaction();

// 1) Insertar en pedidos
$stmt = $conn->prepare("
  INSERT INTO pedidos 
    (usuario_id, fecha, estado, tipo_entrega, direccion)
  VALUES (?, ?, 'pendiente', ?, ?)
");
$stmt->bind_param("isss", $userId, $fecha, $tipoEntrega, $direccion);
$stmt->execute();
$pedidoId = $stmt->insert_id;
$stmt->close();

// 2) Insertar cada línea en detalle_pedido
foreach ($carrito as $prodId => $cant) {
    // 2a) Obtener precio actual
    $p = $conn->prepare("SELECT precio FROM productos WHERE id = ?");
    $p->bind_param("i", $prodId);
    $p->execute();
    $p->bind_result($precio);
    $p->fetch();
    $p->close();

    $sub = $precio * $cant;

    // 2b) Insertar línea
    $d = $conn->prepare("
      INSERT INTO detalle_pedido 
        (pedido_id, producto_id, cantidad, subtotal)
      VALUES (?, ?, ?, ?)
    ");
    $d->bind_param("iiid", $pedidoId, $prodId, $cant, $sub);
    $d->execute();
    $d->close();
}

$conn->commit();

// 3) Limpiar carrito
unset($_SESSION['carrito']);

// 4) Redirigir a página de confirmación
header("Location: /Ferremasa/cliente/confirmacion.php?pedido=$pedidoId");
exit;
?>
