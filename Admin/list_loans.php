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
$user_rol = $_SESSION['user_rol'] ?? '';

// Mostrar todos los préstamos si es admin o super_admin, solo los suyos si es estudiante/docente
if ($user_rol === 'admin' || $user_rol === 'super_admin') {
    $result = $conn->query("
        SELECT prestamos.*, usuarios.nombre, usuarios.gmail_institucional, libros.titulo
        FROM prestamos
        JOIN usuarios ON prestamos.id_usuario = usuarios.id_usuario
        JOIN libros ON prestamos.id_libro = libros.id
        ORDER BY prestamos.id_prestamo DESC
    ");
} else {
    $result = $conn->query("
        SELECT prestamos.*, usuarios.nombre, usuarios.gmail_institucional, libros.titulo
        FROM prestamos
        JOIN usuarios ON prestamos.id_usuario = usuarios.id_usuario
        JOIN libros ON prestamos.id_libro = libros.id
        WHERE prestamos.id_usuario = $id_usuario
        ORDER BY prestamos.id_prestamo DESC
    ");
}


if ($result === false) {
    echo '<div class="alert alert-danger">Error en la consulta SQL: ' . $conn->error . '</div>';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Listado de Préstamos | Admin Dashboard</title>
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
                        <div class="col-12">
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Listado de Préstamos</h4>
                                    <a href="add_loan.php" class="btn btn-light btn-sm">+ Registrar Préstamo</a>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_GET['returned'])): ?>
                                        <div class="alert alert-success">Préstamo marcado como devuelto correctamente.</div>
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Estudiante</th>
                                                    <th>Libro</th>
                                                    <th>Fecha Préstamo</th>
                                                    <th>Fecha Devolución</th>
                                                    <th>Devuelto</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id_prestamo'] ?></td>
                                                    <td><?= htmlspecialchars($row['nombre']) ?> (<?= htmlspecialchars($row['gmail_institucional']) ?>)</td>
                                                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                                                    <td><?= htmlspecialchars($row['fecha_prestamo']) ?></td>
                                                    <td><?= htmlspecialchars($row['fecha_devolucion']) ?></td>
                                                    <td>
                                                    <?php if (in_array(strtolower($row['status']), ['devuelto', 'entregado', 'entregado con retraso'])): ?>
    <span class="badge bg-success">Sí</span>
<?php else: ?>
    <span class="badge bg-danger">No</span>
<?php endif; ?>

                                                    </td>
                                                    <td>
                                                        <?php if ($row['status'] == 'no entregado'): ?>
                                                        <a href="return_loan.php?id=<?= $row['id_prestamo'] ?>" class="btn btn-sm btn-info">Marcar Devuelto</a>
                                                        <?php endif; ?>
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
