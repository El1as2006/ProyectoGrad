<?php
$conn = require "../conexion.php";

if (isset($_GET["id"])) {
    $id = (int) $_GET["id"];

    // Soft delete: marcamos como inactivo
    $stmt = $conn->prepare("UPDATE libros SET estado = 0 WHERE id_book = :id");
    $success = $stmt->execute([':id' => $id]);

    if ($success) {
        header("Location: books.php?delete=success");
        exit();
    } else {
        header("Location: books.php?delete=error");
        exit();
    }
} else {
    header("Location: books.php?delete=invalid");
    exit();
}