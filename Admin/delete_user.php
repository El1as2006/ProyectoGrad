<?php
include_once '../conexion.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: list_users.php');
    exit;
}

$errores = [];
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header('Location: list_users.php?deleted=1');
        exit;
    } else {
        $errores[] = 'Error al eliminar el usuario.';
    }
    $stmt->close();
}

$stmt = $conn->prepare('SELECT nombre, email FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    header('Location: list_users.php');
    exit;
}
$stmt->bind_result($nombre, $email);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Eliminar Usuario | Admin Dashboard</title>
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
                                <div class="card-header bg-danger text-white">
                                    <h4 class="mb-0">Eliminar Usuario</h4>
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
                                        <p>¿Estás seguro que deseas eliminar al usuario <strong><?= htmlspecialchars($nombre) ?> (<?= htmlspecialchars($email) ?>)</strong>?</p>
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                        <a href="list_users.php" class="btn btn-secondary ms-2">Cancelar</a>
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
