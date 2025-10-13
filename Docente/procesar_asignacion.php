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
$estudiante_id = isset($_POST['estudiante_id']) ? intval($_POST['estudiante_id']) : 0;
$libro_id = isset($_POST['libro_id']) ? intval($_POST['libro_id']) : 0;

if ($estudiante_id <= 0 || $libro_id <= 0) {
    die("Datos incompletos o inválidos.");
}

// Validar que el estudiante exista
$stmtCheckUser = $conn->prepare("SELECT COUNT(*) FROM estudiantes WHERE id = ?");
$stmtCheckUser->bind_param("i", $estudiante_id);
$stmtCheckUser->execute();
$stmtCheckUser->bind_result($countUser);
$stmtCheckUser->fetch();
$stmtCheckUser->close();

if ($countUser == 0) {
    die("Error: el estudiante seleccionado no existe en la base de datos.");
}

// Validar que el libro exista y tenga stock
$stmtCheckLibro = $conn->prepare("SELECT stock FROM libros WHERE id = ?");
$stmtCheckLibro->bind_param("i", $libro_id);
$stmtCheckLibro->execute();
$stmtCheckLibro->bind_result($stockLibro);
$stmtCheckLibro->fetch();
$stmtCheckLibro->close();

if ($stockLibro <= 0) {
    die("Error: el libro seleccionado no tiene stock disponible.");
}

// Configuración del préstamo
$fecha_prestamo = date('Y-m-d');
$status = "prestado";
$origen = "docente";

// Usar un id_usuario fijo que ya exista en la tabla usuarios
$id_usuario_fijo = 1;

// Insertar en la tabla prestamos
$stmt = $conn->prepare("INSERT INTO prestamos (id_libro, fecha_prestamo, status, origen_usuario, id_usuario) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssi", $libro_id, $fecha_prestamo, $status, $origen, $id_usuario_fijo);

if ($stmt->execute()) {
    // Reducir stock del libro
    $update = $conn->prepare("UPDATE libros SET stock = stock - 1 WHERE id = ? AND stock > 0");
    $update->bind_param("i", $libro_id);
    $update->execute();
    $update->close();

    echo "<script>alert('📚 Libro asignado correctamente al estudiante'); window.location.href='list_grados.php';</script>";
} else {
    echo "Error al asignar libro: " . $conn->error;
}

$stmt->close();
$conn->close();
