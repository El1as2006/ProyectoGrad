<?php
include '../conexion.php';
session_start();

// Validar sesión - verificar ambas posibles variables de sesión
$id_usuario = null;
if (isset($_SESSION['user_id'])) {
    $id_usuario = $_SESSION['user_id'];
} elseif (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
}

if (!$id_usuario) {
    header('Location: login.php');
    exit;
}

$notificaciones = [];

// Verificar que la conexión existe
if (!$conn) {
    die("Error de conexión a la base de datos");
}

// Preparar la consulta con manejo de errores
$stmt = $conn->prepare('SELECT tipo, mensaje, fecha, leido FROM notificaciones WHERE usuario_id = ? ORDER BY fecha DESC');

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$notificaciones = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Notificaciones | Biblioteca</title>
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
                                    <h4 class="mb-0">Tus Notificaciones</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($notificaciones)): ?>
                                        <div class="alert alert-info">No tienes notificaciones.</div>
                                    <?php else: ?>
                                        <ul class="list-group">
                                            <?php foreach ($notificaciones as $notif): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <strong><?= ucfirst($notif['tipo']) ?>:</strong> <?= htmlspecialchars($notif['mensaje']) ?>
                                                    </span>
                                                    <span class="badge bg-light text-muted"> <?= date('d/m/Y H:i', strtotime($notif['fecha'])) ?> </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
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
