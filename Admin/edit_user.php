<?php
include_once '../conexion.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: list_users.php');
    exit;
}

$mensaje = '';
$errores = [];

// Obtener datos actuales
$stmt = $conn->prepare('SELECT nombre, email, rol, activo FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    header('Location: list_users.php');
    exit;
}
$stmt->bind_result($nombre, $email, $rol, $activo);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rol = trim($_POST['rol'] ?? 'Usuario');
    $activo = isset($_POST['activo']) ? 1 : 0;
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Email inválido.';
    if ($password !== '' && strlen($password) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    if ($password !== $password2) $errores[] = 'Las contraseñas no coinciden.';

    if (empty($errores)) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE usuarios SET nombre=?, email=?, rol=?, activo=?, contrasena=? WHERE id=?');
            $stmt->bind_param('sssisi', $nombre, $email, $rol, $activo, $hash, $id);
        } else {
            $stmt = $conn->prepare('UPDATE usuarios SET nombre=?, email=?, rol=?, activo=? WHERE id=?');
            $stmt->bind_param('sssii', $nombre, $email, $rol, $activo, $id);
        }
        if ($stmt->execute()) {
            $mensaje = 'Usuario actualizado correctamente.';
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
    <title>Editar Usuario | Admin Dashboard</title>
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
                                <div class="card-header bg-warning text-dark">
                                    <h4 class="mb-0">Editar Usuario</h4>
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
                                            <label for="password" class="form-label">Nueva Contraseña (opcional)</label>
                                            <input type="password" class="form-control" id="password" name="password">
                                        </div>
                                        <div class="mb-3">
                                            <label for="password2" class="form-label">Repetir Contraseña</label>
                                            <input type="password" class="form-control" id="password2" name="password2">
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" <?= $activo ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="activo">Activo</label>
                                        </div>
                                        <button type="submit" class="btn btn-warning">Actualizar</button>
                                        <a href="list_users.php" class="btn btn-secondary ms-2">Volver</a>
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
