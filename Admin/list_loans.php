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

if ($user_rol === 'admin' || $user_rol === 'super_admin') {
    $query = "
        SELECT prestamos.*, libros.titulo
        FROM prestamos
        JOIN libros ON prestamos.id_libro = libros.id
        ORDER BY prestamos.id_prestamo DESC
    ";
} else {
    $query = "
        SELECT prestamos.*, libros.titulo
        FROM prestamos
        JOIN libros ON prestamos.id_libro = libros.id
        WHERE prestamos.id_usuario = $id_usuario
        ORDER BY prestamos.id_prestamo DESC
    ";
}

$result = $conn->query($query);
$prestamos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_usuario_prestamo = $row['id_usuario'];

        // Primero busca en estudiantes
        $estudiante_query = $conn->query("SELECT nombre, gmail_institucional FROM estudiantes WHERE id = $id_usuario_prestamo LIMIT 1");
        if ($estudiante_query && $estudiante_query->num_rows > 0) {
            $estudiante = $estudiante_query->fetch_assoc();
            $row['nombre'] = $estudiante['nombre'];
            $row['gmail_institucional'] = $estudiante['gmail_institucional'];
        } else {
            // Si no es estudiante, busca en usuarios
            $usuario_query = $conn->query("SELECT nombre, gmail_institucional FROM usuarios WHERE id_usuario = $id_usuario_prestamo LIMIT 1");
            if ($usuario_query && $usuario_query->num_rows > 0) {
                $usuario = $usuario_query->fetch_assoc();
                $row['nombre'] = $usuario['nombre'];
                $row['gmail_institucional'] = $usuario['gmail_institucional'];
            } else {
                // Si no está en ninguna tabla
                $row['nombre'] = 'Desconocido';
                $row['gmail_institucional'] = 'Desconocido';
            }
        }
        $prestamos[] = $row;
    }
} else {
    echo '<div class="alert alert-warning">No se encontraron préstamos.</div>';
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
                                                    <th>Estudiante/Usuario</th>
                                                    <th>Libro</th>
                                                    <th>Fecha Préstamo</th>
                                                    <th>Fecha Devolución</th>
                                                    <th>Devuelto</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($prestamos)): ?>
                                                    <?php foreach($prestamos as $row): ?>
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
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">No hay préstamos registrados.</td>
                                                    </tr>
                                                <?php endif; ?>
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
    <script src="includes/notifications.js"></script>
</body>
</html>
