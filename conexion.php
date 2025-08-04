<?php
$host = "localhost";
$user = "root";
$password = ""; // o tu contraseña si la tienes
$database = "biblioteca"; // Asegúrate de que este sea el nombre correcto

$conn = mysqli_connect($host, $user, $password, $database);

// Verificación de conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
return $conn;
?>