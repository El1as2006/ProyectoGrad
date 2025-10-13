<?php
// detalle_prestamo.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include '../conexion.php';
$id_usuario = $_SESSION['user_id'] ?? null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$prestamo = null;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT p.*, u.nombre AS estudiante, l.titulo AS libro FROM prestamos p JOIN usuarios u ON p.id_usuario = u.id_usuario JOIN libros l ON p.id_libro = l.id WHERE p.id_prestamo = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $prestamo = $res->fetch_assoc();
    $stmt->close();
}
$qr_path = $prestamo ? '../uploads/qr/prestamo_' . $prestamo['id_prestamo'] . '.png' : '';

// Devolución automática por QR (solo admin/super_admin)
$devolucionMsg = '';
if ($prestamo && isset($_GET['accion']) && $_GET['accion'] === 'devolver') {
    $rol = $_SESSION['rol'] ?? '';
    if (in_array($rol, ['admin', 'super_admin'])) {
        if (strtolower($prestamo['status']) !== 'devuelto' && strtolower($prestamo['status']) !== 'entregado' && strtolower($prestamo['status']) !== 'entregado con retraso') {
            $stmt = $conn->prepare("UPDATE prestamos SET status = 'devuelto' WHERE id_prestamo = ?");
            $stmt->bind_param('i', $prestamo['id_prestamo']);
            if ($stmt->execute()) {
                $devolucionMsg = '<div class="alert alert-success">Préstamo marcado como devuelto correctamente.</div>';
            } else {
                $devolucionMsg = '<div class="alert alert-danger">Error al marcar como devuelto.</div>';
            }
            $stmt->close();
            // Actualizar estado en $prestamo para mostrarlo actualizado
            $prestamo['status'] = 'devuelto';
        } else {
            $devolucionMsg = '<div class="alert alert-info">Este préstamo ya estaba marcado como devuelto.</div>';
        }
    } else {
        $devolucionMsg = '<div class="alert alert-danger">No tienes permisos para realizar esta acción.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Detalle del Préstamo</title>
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-5">
        <?= $devolucionMsg ?>
        <?php if ($prestamo): ?>
            <h2>Préstamo #<?= $prestamo['id_prestamo'] ?></h2>
            <p><b>Estudiante:</b> <?= htmlspecialchars($prestamo['estudiante']) ?></p>
            <p><b>Libro:</b> <?= htmlspecialchars($prestamo['libro']) ?></p>
            <p><b>Fecha Préstamo:</b> <?= htmlspecialchars($prestamo['fecha_prestamo']) ?></p>
            <p><b>Fecha Devolución:</b> <?= htmlspecialchars($prestamo['fecha_devolucion']) ?></p>
            <p><b>Estado:</b> <?= htmlspecialchars($prestamo['status']) ?></p>
            <p><b>QR del préstamo:</b><br>
                <?php if (file_exists($qr_path)): ?>
                    <img src="<?= $qr_path ?>" alt="QR Préstamo" style="width:180px;">
                <?php else: ?>
                    <span class="text-danger">QR no disponible</span>
                <?php endif; ?>
            </p>
        <?php else: ?>
            <div class="alert alert-danger">Préstamo no encontrado.</div>
        <?php endif; ?>
        <a href="list_loans.php" class="btn btn-primary mt-3">Volver a la lista</a>
    </div>
</body>
</html>
