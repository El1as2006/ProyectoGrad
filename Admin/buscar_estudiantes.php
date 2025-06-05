<?php
include '../conexion.php';

$mensaje = '';
$errores = [];
$busqueda = trim($_GET['q'] ?? '');

$sql = 'SELECT id_usuario, nombre, gmail_institucional, telefono FROM usuarios WHERE rol = "estudiante"';
$params = [];
$types = '';
if ($busqueda !== '') {
    $sql .= ' AND (nombre LIKE ? OR gmail_institucional LIKE ? OR telefono LIKE ?)';
    $like = "%$busqueda%";
    $params = [$like, $like, $like];
    $types = 'sss';
}
$sql .= ' ORDER BY id_usuario DESC';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Buscar Estudiantes | Admin Dashboard</title>
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
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card mt-4">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Buscar Estudiantes</h4>
                                    <a href="add_student.php" class="btn btn-light btn-sm">+ Añadir Estudiante</a>
                                </div>
                                <div class="card-body">
                                    <form class="mb-3" method="get">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="q" placeholder="Buscar por nombre, correo o teléfono" value="<?= htmlspecialchars($busqueda) ?>">
                                            <button class="btn btn-info" type="submit">Buscar</button>
                                            <a href="buscar_estudiantes.php" class="btn btn-outline-secondary">Limpiar</a>
                                        </div>
                                    </form>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Teléfono</th>
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
                                                    <td>
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
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
