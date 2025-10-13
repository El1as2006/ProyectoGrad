<?php
session_start();
include '../conexion.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: list_loans.php');
    exit;
}

$errores = [];
$mensaje = '';

// Obtener datos del préstamo
$stmt = $conn->prepare('SELECT id_libro, status FROM prestamos WHERE id_prestamo = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    header('Location: list_loans.php');
    exit;
}

$stmt->bind_result($libro_id, $status);
$stmt->fetch();
$stmt->close();

// Si ya está devuelto, redirige
if (strtolower($status) === 'devuelto') {
    header('Location: list_loans.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_actual = date('Y-m-d');

    // Marcar préstamo como devuelto con fecha
    $stmt = $conn->prepare("UPDATE prestamos SET status = 'devuelto', fecha_devolucion = ? WHERE id_prestamo = ?");
    $stmt->bind_param('si', $fecha_actual, $id);

    if ($stmt->execute()) {
        $stmt->close();

        // Incrementar stock del libro
        $update_stock = $conn->prepare("UPDATE libros SET stock = stock + 1 WHERE id = ?");
        $update_stock->bind_param("i", $libro_id);
        $update_stock->execute();
        $update_stock->close();

        // Obtener usuario del préstamo
        $stmtUser = $conn->prepare('SELECT id_usuario FROM prestamos WHERE id_prestamo = ?');
        $stmtUser->bind_param('i', $id);
        $stmtUser->execute();
        $stmtUser->bind_result($usuario_id);
        $stmtUser->fetch();
        $stmtUser->close();

        // Obtener correo y nombre del usuario
        $stmtCorreo = $conn->prepare('SELECT gmail_institucional, nombre FROM usuarios WHERE id_usuario = ?');
        $stmtCorreo->bind_param('i', $usuario_id);
        $stmtCorreo->execute();
        $stmtCorreo->bind_result($correo_est, $nombre_est);
        $stmtCorreo->fetch();
        $stmtCorreo->close();

        // Enviar correo de notificación
        $asunto = 'Libro devuelto correctamente';
        $cuerpo = "Hola $nombre_est,\n\nSe ha registrado la devolución de tu libro en la biblioteca.\n\n¡Gracias por usar el servicio!";
        @mail($correo_est, $asunto, $cuerpo, "From: biblioteca@tudominio.com");

        // Registrar notificación en base de datos
        $tipo = 'devolucion';
        $mensaje_notif = 'Libro devuelto correctamente.';
        $stmtNotif = $conn->prepare('INSERT INTO notificaciones (usuario_id, tipo, mensaje) VALUES (?, ?, ?)');
        $stmtNotif->bind_param('iss', $usuario_id, $tipo, $mensaje_notif);
        $stmtNotif->execute();
        $stmtNotif->close();

        header('Location: list_loans.php?returned=1');
        exit;
    } else {
        $errores[] = 'Error al marcar como devuelto.';
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Marcar Préstamo Devuelto | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php include_once 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card mt-4">
                                <div class="card-header bg-info text-white">
                                    <h4 class="mb-0">Marcar Préstamo como Devuelto</h4>
                                </div>
                                <div class="card-body">
                                    <?php if ($errores): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errores as $e) echo "<li>$e</li>"; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post">
                                        <p>¿Estás seguro que deseas marcar este préstamo como devuelto?</p>
                                        <button type="submit" class="btn btn-info">Marcar Devuelto</button>
                                        <a href="list_loans.php" class="btn btn-secondary ms-2">Cancelar</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
