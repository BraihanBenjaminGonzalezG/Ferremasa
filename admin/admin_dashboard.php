<?php
session_start();
if ($_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
echo "Bienvenido Admin";
?>
