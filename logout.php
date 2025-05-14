<?php
session_start();

// Elimina todas las variables de sesión
$_SESSION = [];

// Destruye la sesión
session_unset();
session_destroy();

// Redirige al login
header("Location: login.php");
exit;
?>







<a href="logout.php">Cerrar sesión</a>


