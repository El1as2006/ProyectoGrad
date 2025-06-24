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

// Consulta unificada para usuarios y estudiantes
$query = "
    SELECT id_usuario AS id, nombre, gmail_institucional, rol, activo, 'usuarios' AS tipo 
    FROM usuarios
    UNION
    SELECT id AS id, nombre, gmail_institucional, rol, activo, 'estudiante' AS tipo 
    FROM estudiantes
    ORDER BY id DESC
";

$result = $conn->query($query);

$usuarios = []; // Reiniciamos el array

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
} else {
    echo '<div class="alert alert-warning">No hay usuarios ni estudiantes registrados.</div>';
}


// Debug opcional para ver los datos
// echo '<pre>'; print_r($usuarios); echo '</pre>';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Listado de Usuarios | Admin Dashboard</title>
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
                                    <h4 class="mb-0">Listado de Usuarios</h4>
                                    <a href="add_user.php" class="btn btn-light btn-sm">+ Añadir Usuario</a>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_GET['deleted'])): ?>
                                        <div class="alert alert-success">Usuario eliminado correctamente.</div>
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead class="table-light">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Activo</th>
        <th>Tipo</th>
        <th>Acciones</th>
    </tr>
</thead>
<tbody>
    <?php if (!empty($usuarios)): ?>
        <?php foreach ($usuarios as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['gmail_institucional']) ?></td>
            <td><?= htmlspecialchars($row['rol']) ?></td>
            <td><?= $row['activo'] ? 'Sí' : 'No' ?></td>
            <td><?= ucfirst($row['tipo']) ?></td>
            <td>
                <?php if ($row['tipo'] == 'usuario'): ?>
                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">Eliminar</a>
                <?php else: ?>
                    <a href="edit_student.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="delete_student.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este estudiante?')">Eliminar</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="text-center">No hay usuarios ni estudiantes registrados.</td>
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
</body>
</html>
