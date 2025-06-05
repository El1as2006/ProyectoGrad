<?php
include_once '../conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$nombre = $email = $rol = '';
$activo = 1;
$mensaje = '';
$errores = [];

$id_usuario = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = trim($_POST['rol'] ?? 'Usuario');
    $activo = isset($_POST['activo']) ? 1 : 0;
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Email inválido.';
    if ($password === '' || strlen($password) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    if ($password !== $password2) $errores[] = 'Las contraseñas no coinciden.';

    if (empty($errores)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO usuarios (nombre, gmail_institucional, rol, activo, contrasena) VALUES (?, ?, ?, ?, ?)');
        if (!$stmt) {
            $errores[] = 'Error en la consulta: ' . $conn->error;
        } else {
            $stmt->bind_param('sssis', $nombre, $email, $rol, $activo, $hash);
            if ($stmt->execute()) {
                $mensaje = 'Usuario añadido correctamente.';
                $nombre = $email = $rol = '';
                $activo = 1;
            } else {
                $errores[] = 'Error al guardar en la base de datos.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Añadir Usuario | Admin Dashboard</title>
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
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">Añadir Usuario</h4>
                                </div>
                                <div class="card-body">
                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-success"> <?= $mensaje ?> </div>
                                    <?php endif; ?>
                                    <?php if (!empty($errores)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errores as $e) echo "<li>$e</li>"; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post" novalidate>
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rol" class="form-label">Rol</label>
                                            <select class="form-select" id="rol" name="rol">
                                                <option value="Usuario" <?= $rol == 'Usuario' ? 'selected' : '' ?>>Usuario</option>
                                                <option value="Admin" <?= $rol == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password2" class="form-label">Repetir Contraseña</label>
                                            <input type="password" class="form-control" id="password2" name="password2" required>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" <?= $activo ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="activo">Activo</label>
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
