<?php
session_start();
include __DIR__ . '/includes/db.php';
include __DIR__ . '/includes/header.php';

// 1) Calculamos total del carrito
$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    header("Location: /Ferremasa/catalogo.php");
    exit;
}
$ids = array_keys($carrito);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT id, precio FROM productos WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
while ($row = $result->fetch_assoc()) {
    $pid  = $row['id'];
    $cant = $carrito[$pid];
    $total += $row['precio'] * $cant;
}
?>

<h2>Checkout</h2>
<p><strong>Total a pagar:</strong> $<?php echo number_format($total, 0, ',', '.'); ?></p>

<form id="checkout-form" method="POST" action="/Ferremasa/carrito/procesar_pedido.php">
  <!-- Enviamos total y direccion a procesar_pedido.php -->
  <input type="hidden" name="total" value="<?php echo $total; ?>">
  
  <fieldset>
    <legend>1. Pago con Webpay</legend>
    <button type="button" id="btn-webpay">Pagar $<?php echo number_format($total, 0, ',', '.'); ?> con Webpay</button>
  </fieldset>

  <fieldset>
    <legend>2. Dirección de entrega</legend>
    <label for="direccion">Dirección:</label><br>
    <input type="text" id="direccion" name="direccion" required placeholder="Ingresa tu dirección"><br><br>
    <button type="button" id="btn-locate">Usar mi ubicación</button>
  </fieldset>

  <button type="submit">Finalizar compra</button>
</form>

<script>
// --- Webpay API (ejemplo) ---
document.getElementById('btn-webpay').addEventListener('click', () => {
  fetch('/Ferremasa/api/webpay/initiate.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      amount: <?php echo $total; ?>,
      sessionId: '<?php echo session_id(); ?>'
    })
  })
  .then(res => res.json())
  .then(data => {
    // data.url llega desde tu API de ejemplo
    window.location.href = data.url;
  })
  .catch(err => alert('Error al iniciar pago Webpay'));
});

// --- Location API + Google Geocoding ---
document.getElementById('btn-locate').addEventListener('click', () => {
  if (!navigator.geolocation) {
    return alert('Tu navegador no soporta geolocalización');
  }
  navigator.geolocation.getCurrentPosition(pos => {
    const { latitude: lat, longitude: lng } = pos.coords;
    fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=TU_API_KEY`)
      .then(r => r.json())
      .then(res => {
        if (res.results && res.results.length) {
          document.getElementById('direccion').value = res.results[0].formatted_address;
        }
      });
  }, () => alert('No fue posible obtener tu ubicación'));
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
