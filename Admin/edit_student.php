<?php
include '../conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$id_usuario = $_SESSION['user_id'] ?? null;

if (!$id_usuario) {
    header('Location: list_students.php');
    exit;
}

$mensaje = '';
$errores = [];

// Obtener datos actuales
$stmt = $conn->prepare('SELECT nombre, gmail_institucional, telefono, carnet FROM usuarios WHERE id_usuario = ? AND rol = "estudiante"');
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    header('Location: list_students.php');
    exit;
}
$stmt->bind_result($nombre, $email, $telefono, $carnet);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['gmail_institucional'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $carnet = trim($_POST['carnet'] ?? '');

    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
    if ($email === '') $errores[] = 'El correo institucional es obligatorio.';
    if ($carnet === '' || !preg_match('/^\d{8}$/', $carnet)) $errores[] = 'El carnet debe tener 8 dígitos.';

    // Validar carnet único
    if ($carnet !== '') {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM usuarios WHERE carnet = ? AND id_usuario != ?');
        $stmt->bind_param('si', $carnet, $id_usuario);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) $errores[] = 'El carnet ya está registrado.';
    }

    if (empty($errores)) {
        $stmt = $conn->prepare('UPDATE usuarios SET nombre=?, gmail_institucional=?, telefono=?, carnet=? WHERE id_usuario=? AND rol="estudiante"');
        $stmt->bind_param('ssssi', $nombre, $email, $telefono, $carnet, $id_usuario);
        if ($stmt->execute()) {
            $mensaje = 'Estudiante actualizado correctamente.';
        } else {
            $errores[] = 'Error al actualizar en la base de datos.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Editar Estudiante | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/session_check.php'; ?>
        <?php include 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card mt-4">
                                <div class="card-header bg-warning text-dark">
                                    <h4 class="mb-0">Editar Estudiante</h4>
                                </div>
                                <div class="card-body">
                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-success"> <?= $mensaje ?> </div>
                                    <?php endif; ?>
                                    <?php if ($errores): ?>
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
                                            <label for="carnet" class="form-label">Carnet (8 dígitos)</label>
                                            <input type="text" class="form-control" id="carnet" name="carnet" pattern="\d{8}" maxlength="8" required value="<?= htmlspecialchars($carnet) ?>">
                                        </div>
                                        <button type="submit" class="btn btn-warning">Actualizar</button>
                                        <a href="list_students.php" class="btn btn-secondary ms-2">Volver</a>
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
