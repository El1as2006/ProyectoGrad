<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$grados = $conn->query("SELECT DISTINCT grado FROM estudiantes ORDER BY grado");
$secciones = $conn->query("SELECT DISTINCT seccion FROM estudiantes ORDER BY seccion");

$grado = $_GET['grado'] ?? '';
$seccion = $_GET['seccion'] ?? '';

$query = "SELECT id, nombre, grado, seccion FROM estudiantes WHERE 1=1";
$params = [];
$types = '';

if ($grado !== '') {
    $query .= " AND grado = ?";
    $params[] = $grado;
    $types .= 's';
}

if ($seccion !== '') {
    $query .= " AND seccion = ?";
    $params[] = $seccion;
    $types .= 's';
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Filtrar Estudiantes</title>
    <link href="../assets/css/vendor.min.css" rel="stylesheet" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" />
</head>
<body>
    <div class="wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row">

                        <!-- Formulario de filtro -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Filtrar Estudiantes</h4>
                                    <form method="get">
                                        <div class="mb-3">
                                            <label for="grado" class="form-label">Grado</label>
                                            <select class="form-control" id="grado" name="grado">
                                                <option value="">Todos</option>
                                                <?php while ($g = $grados->fetch_assoc()): ?>
                                                    <option value="<?= $g['grado'] ?>" <?= $grado == $g['grado'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($g['grado']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="seccion" class="form-label">Sección</label>
                                            <select class="form-control" id="seccion" name="seccion">
                                                <option value="">Todas</option>
                                                <?php while ($s = $secciones->fetch_assoc()): ?>
                                                    <option value="<?= $s['seccion'] ?>" <?= $seccion == $s['seccion'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($s['seccion']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de estudiantes -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Listado de Estudiantes</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" style="font-size: 19px;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Grado</th>
                                                    <th>Sección</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($resultado && $resultado->num_rows > 0): ?>
                                                    <?php while ($row = $resultado->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?= $row['id'] ?></td>
                                                            <td><?= htmlspecialchars($row['nombre']) ?></td>
                                                            <td><?= htmlspecialchars($row['grado']) ?></td>
                                                            <td><?= htmlspecialchars($row['seccion']) ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="4" class="text-center">No se encontraron estudiantes.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- row -->
                </div> <!-- container -->
            </div> <!-- content -->
        </div> <!-- content-page -->
    </div> <!-- wrapper -->

    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
