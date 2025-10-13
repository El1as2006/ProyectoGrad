<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: .../Admin/login.php');
    exit;
}

include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_prestamo = intval($_POST['id_prestamo'] ?? 0);

    if ($id_prestamo > 0) {
        $stmt = $conn->prepare("DELETE FROM prestamos WHERE id_prestamo = ?");
        $stmt->bind_param("i", $id_prestamo);

        if ($stmt->execute()) {
            header("Location: list_asignaciones.php?deleted=1");
            exit;
        } else {    
            echo "Error al eliminar la asignación: " . $conn->error;
        }
    } else {
        echo "ID de préstamo inválido.";
    }
} else {
    header("Location: list_asignaciones.php");
    exit;
}