<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once '../conexion.php';

// Historial de préstamos por estudiante
$estudiante_id = $_GET['id'] ?? null;
if (!$estudiante_id || !is_numeric($estudiante_id)) {
    header('Location: list_students.php');
    exit;
}

$stmt = $conn->prepare('SELECT e.nombre, e.apellido FROM estudiantes e WHERE e.id = ?');
$stmt->bind_param('i', $estudiante_id);
$stmt->execute();
$stmt->bind_result($nombre, $apellido);
$stmt->fetch();
$stmt->close();

$sql = 'SELECT l.titulo, p.fecha_prestamo, p.fecha_devolucion, p.devuelto
        FROM prestamos p
        JOIN libros l ON p.libro_id = l.id
        WHERE p.estudiante_id = ?
        ORDER BY p.fecha_prestamo DESC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $estudiante_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Historial de Préstamos de Estudiante | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php include_once 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card mt-4">
                                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Historial de Préstamos de <?= htmlspecialchars($apellido) ?>, <?= htmlspecialchars($nombre) ?></h4>
                                    <a href="list_students.php" class="btn btn-light btn-sm">Volver a Estudiantes</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Título del Libro</th>
                                                    <th>Fecha Préstamo</th>
                                                    <th>Fecha Devolución</th>
                                                    <th>Devuelto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($row = $result->fetch_assoc()): ?>
                                                <tr>
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
        </div>
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
