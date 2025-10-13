<?php
include_once '../conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['user_id'] ?? null;

$sql = 'SELECT p.id, e.nombre, e.apellido, l.titulo, p.fecha_prestamo, p.fecha_devolucion, p.devuelto
        FROM prestamos p
        JOIN estudiantes e ON p.estudiante_id = e.id
        JOIN libros l ON p.libro_id = l.id
        ORDER BY p.id DESC';
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Historial de Préstamos | Admin Dashboard</title>
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
                                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Historial de Préstamos</h4>
                                </div>
                                <div class="card-body">
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= htmlspecialchars($row['apellido'] . ', ' . $row['nombre']) ?></td>
                                                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                                                    <td><?= htmlspecialchars($row['fecha_prestamo']) ?></td>
                                                    <td><?= htmlspecialchars($row['fecha_devolucion']) ?></td>
                                                    <td>
                                                        <?php if ($row['devuelto']): ?>
                                                            <span class="badge bg-success">Sí</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">No</span>
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
</body>
</html>
