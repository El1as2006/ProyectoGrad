<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$nombre = $email = $telefono = $mensaje = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['gmail_institucional'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $carnet = trim($_POST['carnet'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nombre === '') { $errores[] = 'El nombre es obligatorio.'; }
    if ($email === '') { $errores[] = 'El correo institucional es obligatorio.'; }
    if ($carnet === '' || !preg_match('/^\d{8}$/', $carnet)) { $errores[] = 'El carnet debe tener 8 dígitos.'; }
    if ($password === '' || strlen($password) < 6) { $errores[] = 'La contraseña debe tener al menos 6 caracteres.'; }
    // Validar carnet único
    if ($carnet !== '') {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM usuarios WHERE carnet = ?');
        $stmt->bind_param('s', $carnet);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) { $errores[] = 'El carnet ya está registrado.'; }
    }

    if (empty($errores)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO usuarios (nombre, gmail_institucional, telefono, carnet, contrasena, rol) VALUES (?, ?, ?, ?, ?, "estudiante")');
        $stmt->bind_param('sssss', $nombre, $email, $telefono, $carnet, $password_hash);
        if ($stmt->execute()) {
            $usuario_id = $conn->insert_id;
            // Generar QR del carnet
            require_once '../assets/phpqrcode/qrcode.php';
            $qr_dir = '../uploads/qr/';
            if (!is_dir($qr_dir)) { mkdir($qr_dir, 0777, true); }
            $qr_data = $carnet;
            $qr_file = $qr_dir . 'carnet_' . $carnet . '.png';
            $qr = QRCode::getMinimumQRCode($qr_data, QR_ERROR_CORRECT_LEVEL_L);
            $img = $qr->createImage(6, 2);
            imagepng($img, $qr_file);
            imagedestroy($img);
            $mensaje = 'Estudiante añadido correctamente. QR generado.';
            // Enviar correo de bienvenida
            $asunto = 'Bienvenido a la Biblioteca Estudiantil';
            $cuerpo = "Hola $nombre,\n\nTu registro en la Biblioteca Estudiantil ha sido exitoso.\n\nPuedes acceder con tu correo institucional y la contraseña que registraste.\n\n¡Bienvenido!";
            @mail($email, $asunto, $cuerpo, "From: biblioteca@tudominio.com");
            // Registrar notificación en la base de datos
            $tipo = 'registro';
            $mensaje_notif = 'Registro exitoso en la biblioteca.';
            $stmtNotif = $conn->prepare('INSERT INTO notificaciones (usuario_id, tipo, mensaje) VALUES (?, ?, ?)');
            $stmtNotif->bind_param('iss', $usuario_id, $tipo, $mensaje_notif);
            $stmtNotif->execute();
            $stmtNotif->close();
            $nombre = $email = $telefono = $carnet = '';
        } else {
            $errores[] = 'Error al guardar en la base de datos.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Añadir Estudiante | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">Registrar Estudiante</h4>
                                </div>
                                <div class="card-body">
                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-success"> <?= $mensaje ?> </div>
                                    <?php endif; ?>
                                    <?php if (!empty($errores)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errores as $e) { echo "<li>$e</li>"; } ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post" novalidate>
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gmail_institucional" class="form-label">Correo institucional</label>
                                            <input type="email" class="form-control" id="gmail_institucional" name="gmail_institucional" value="<?= htmlspecialchars($email) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                        </div>
                                        <div class="mb-3">
                                            <label for="carnet" class="form-label">Carnet (8 dígitos)</label>
                                            <input type="text" class="form-control" id="carnet" name="carnet" pattern="\d{8}" maxlength="8" required value="<?= htmlspecialchars($carnet ?? '') ?>">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
