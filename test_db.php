<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "ferremasa_db";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Conexión fallida: " . $conn->connect_error);
}
echo "✅ Conexión exitosa a la base de datos";
?>

