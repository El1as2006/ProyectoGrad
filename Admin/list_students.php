<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$id_usuario = $_SESSION['user_id'] ?? null;

// Mostrar solo usuarios con rol estudiante
$result = $conn->query("SELECT id_usuario, nombre, gmail_institucional, telefono, carnet FROM usuarios WHERE rol = 'estudiante' ORDER BY id_usuario DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Listado de Estudiantes | Admin Dashboard</title>
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
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Listado de Estudiantes</h4>
                            <a href="add_student.php" class="btn btn-light btn-sm">+ Añadir Estudiante</a>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['deleted'])): ?>
                                <div class="alert alert-success">Estudiante eliminado correctamente.</div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Carnet</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id_usuario'] ?></td>
                                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                                            <td><?= htmlspecialchars($row['gmail_institucional']) ?></td>
                                            <td><?= htmlspecialchars($row['telefono'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($row['carnet'] ?? '') ?></td>
                                            <td>
                                                <a href="ficha_estudiante.php?id=<?= $row['id_usuario'] ?>" class="btn btn-sm btn-info">Ver ficha</a>
                                                <a href="edit_student.php?id=<?= $row['id_usuario'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                                <a href="delete_student.php?id=<?= $row['id_usuario'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este estudiante?')">Eliminar</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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
    <!-- Script para notificaciones -->
    <script src="includes/notifications.js"></script>
</body>
</html>
