<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

// Recibir datos del formulario
$estudiante_id = $_POST['estudiante_id'] ?? '';
$libro_id = $_POST['libro_id'] ?? '';

if ($estudiante_id === '' || $libro_id === '') {
    die("Datos incompletos.");
}

// Validar que el estudiante exista en la tabla usuarios
$stmtCheckUser = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE id_usuario = ?");
$stmtCheckUser->bind_param("i", $estudiante_id);
$stmtCheckUser->execute();
$stmtCheckUser->bind_result($countUser);
$stmtCheckUser->fetch();
$stmtCheckUser->close();

if ($countUser == 0) {
    die("Error: el estudiante seleccionado no existe en la base de datos.");
}

// Configuración del préstamo
$fecha_prestamo = date('Y-m-d');
$status = "prestado";
$origen = "docente"; // opcional, para diferenciar

// Insertar en la tabla prestamos
$stmt = $conn->prepare("INSERT INTO prestamos (id_libro, fecha_prestamo, status, origen_usuario, id_usuario) 
                        VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssi", $libro_id, $fecha_prestamo, $status, $origen, $estudiante_id);

if ($stmt->execute()) {
    // Reducir stock del libro
    $update = $conn->prepare("UPDATE libros SET stock = stock - 1 WHERE id = ? AND stock > 0");
    $update->bind_param("i", $libro_id);
    $update->execute();
    $update->close();

    echo "<script>alert('📚 Libro asignado correctamente al estudiante'); 
          window.location.href='list_grados.php';</script>";
} else {
    echo "Error al asignar libro: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
