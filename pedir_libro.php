<?php
session_start();
$conexion = include_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['libro_id'], $_POST['fecha_devolucion'])) {
    $libro_id = intval($_POST['libro_id']);
    $id_usuario = $_SESSION['user_id'] ?? null;
    $fecha_devolucion = $_POST['fecha_devolucion'];

    if (!$id_usuario) {
        header("Location: /ProyectoGrad/login.php");
        exit;
    }

    // Validar fecha (muy básico)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_devolucion)) {
        die("Fecha de devolución inválida.");
    }

    // Verifica que el libro exista y tenga stock
    $stmt = $conexion->prepare("SELECT stock FROM libros WHERE id = ?");
    $stmt->bind_param("i", $libro_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        die("Libro no encontrado.");
    }

    $libro = $resultado->fetch_assoc();

    if ($libro['stock'] <= 0) {
        header("Location: /ProyectoGrad/libro_detalle.php?id=$libro_id&error=nostock");
        exit;
    }

    // Insertar préstamo
    $fecha_prestamo = date('Y-m-d');
    $status = 'no entregado';

    $insert = $conexion->prepare("
        INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, status)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("iisss", $id_usuario, $libro_id, $fecha_prestamo, $fecha_devolucion, $status);

    if (!$insert->execute()) {
        die("Error al registrar el préstamo: " . $conexion->error);
    }

    // Reducir el stock del libro
    $update = $conexion->prepare("UPDATE libros SET stock = stock - 1 WHERE id = ?");
    $update->bind_param("i", $libro_id);
    $update->execute();

    header("Location: /ProyectoGrad/libro_detalle.php?id=$libro_id&prestamo=ok");
    exit;
}
header("Location: /ProyectoGrad/catalogo.php");
exit;
