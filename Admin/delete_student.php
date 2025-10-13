<?php
include '../conexion.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: list_students.php');
    exit;
}

$errores = [];
$mensaje = '';

// Eliminar estudiante si existe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM usuarios WHERE id_usuario = ? AND rol = "estudiante"');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        header('Location: list_students.php?deleted=1');
        exit;
    } else {
        $errores[] = 'Error al eliminar el estudiante.';
    }
    $stmt->close();
}

// Obtener datos para mostrar
$stmt = $conn->prepare('SELECT nombre FROM usuarios WHERE id_usuario = ? AND rol = "estudiante"');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    header('Location: list_students.php');
    exit;
}
$stmt->bind_result($nombre);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Eliminar Estudiante | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        include 'includes/session_check.php';
        include 'includes/sidebar.php';
        ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card mt-4">
                                <div class="card-header bg-danger text-white">
                                    <h4 class="mb-0">Eliminar Estudiante</h4>
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
                                        <p>¿Estás seguro que deseas eliminar al estudiante <strong><?= htmlspecialchars($nombre) ?></strong>?</p>
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                        <a href="list_students.php" class="btn btn-secondary ms-2">Cancelar</a>
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
