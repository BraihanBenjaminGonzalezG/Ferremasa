<?php
// catalogo.php
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/db.php';
include __DIR__ . '/models/producto_model.php';

// Traemos todos los productos
$productos = getAllProducts($conn);
?>

<h2>Catálogo de Productos</h2>

<?php if (empty($productos)): ?>
  <p>No hay productos disponibles en este momento.</p>
<?php else: ?>
  <div class="product-grid">
    <?php foreach ($productos as $p): ?>
      <div class="product-card">
        <?php if (!empty($p['imagen'])): ?>
          <img src="/FERREMASA/assets/img/<?php echo htmlspecialchars($p['imagen']); ?>"
               alt="<?php echo htmlspecialchars($p['nombre']); ?>">
        <?php else: ?>
          <div class="no-image">Sin imagen</div>
        <?php endif; ?>

        <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
        <p class="descripcion">
          <?php echo nl2br(htmlspecialchars($p['descripcion'])); ?>
        </p>
        <p class="precio">$<?php echo number_format($p['precio'], 0, ',', '.'); ?></p>
        <form action="/FERREMASA/carrito/agregar.php" method="POST">
          <input type="hidden" name="producto_id" value="<?php echo $p['id']; ?>">
          <label>
            Cantidad:
            <input type="number" name="cantidad" value="1" min="1" max="<?php echo $p['stock']; ?>">
          </label>
          <button type="submit">Agregar al carrito</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
