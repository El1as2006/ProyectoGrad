<?php
// detalle_libro.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$libro = null;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT * FROM libros WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $libro = $res->fetch_assoc();
    $stmt->close();
}
$qr_path = $libro ? '../uploads/qr/libro_' . $libro['id'] . '.png' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Detalle del Libro</title>
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <?php if ($libro): ?>
            <h2><?= htmlspecialchars($libro['titulo']) ?></h2>
            <p><b>Autor:</b> <?= htmlspecialchars($libro['autor']) ?></p>
            <p><b>Género:</b> <?= htmlspecialchars($libro['genero']) ?></p>
            <p><b>Año:</b> <?= htmlspecialchars($libro['año_publicacion']) ?></p>
            <p><b>ISBN:</b> <?= htmlspecialchars($libro['isbn']) ?></p>
            <p><b>Descripción:</b> <?= htmlspecialchars($libro['descripcion']) ?></p>
            <p><b>QR del libro:</b><br>
                <?php if (file_exists($qr_path)): ?>
                    <img src="<?= $qr_path ?>" alt="QR Libro" style="width:180px;">
                <?php else: ?>
                    <span class="text-danger">QR no disponible</span>
                <?php endif; ?>
            </p>
        <?php else: ?>
            <div class="alert alert-danger">Libro no encontrado.</div>
        <?php endif; ?>
        <a href="list_books.php" class="btn btn-primary mt-3">Volver a la lista</a>
    </div>
</body>
</html>
