<?php
// models/producto_model.php

/**
 * Obtiene todos los productos de la base de datos.
 *
 * @param mysqli $conn Conexión a la base de datos
 * @return array      Array de productos (cada uno como array asociativo)
 */
function getAllProducts($conn) {
    $sql = "SELECT id, nombre, descripcion, precio, stock, imagen FROM productos WHERE stock > 0";
    $result = $conn->query($sql);

    $productos = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
    return $productos;
}
