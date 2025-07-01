<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

// Cargar grados, secciones, especialidades
$grados = $conn->query("SELECT DISTINCT grado FROM estudiantes ORDER BY grado");
$secciones = $conn->query("SELECT DISTINCT seccion FROM estudiantes ORDER BY seccion");
$especialidades = $conn->query("SELECT DISTINCT especialidad FROM estudiantes ORDER BY especialidad");

// Filtros
$grado = $_GET['grado'] ?? '';
$seccion = $_GET['seccion'] ?? '';
$especialidad = $_GET['especialidad'] ?? '';

$query = "SELECT id, nombre, grado, seccion, especialidad FROM estudiantes WHERE 1=1";
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
if ($especialidad !== '') {
    $query .= " AND especialidad = ?";
    $params[] = $especialidad;
    $types .= 's';
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Agrupar estudiantes por grupos
$grupos = [];
while ($row = $resultado->fetch_assoc()) {
    $esp = strtolower(trim($row['especialidad']));
    if ($esp == 'tc' || $esp == '') {
        $grupo = "{$row['grado']}{$row['seccion']}";
    } else {
        $grupo = "{$row['grado']} " . strtoupper($row['especialidad']);
    }
    $grupos[$grupo][] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estudiantes Agrupados</title>
    <link rel="stylesheet" href="../assets/css/vendor.min.css">
    <link rel="stylesheet" href="../assets/css/app-saas.min.css">
    <link rel="stylesheet" href="../assets/css/icons.min.css">
    <style>
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            padding: 20px;
            text-align: center;
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
        }
        .card-icon {
            font-size: 42px;
            color: #007bff;
            margin-bottom: 12px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .card-sub {
            font-size: 14px;
            color: #666;
            margin-bottom: 12px;
        }
        .btn {
            display: inline-block;
            padding: 6px 14px;
            background: #007bff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: background .3s;
            cursor: pointer;
        }
        .btn:hover {
            background: #0056b3;
        }
        ul.estudiantes {
            text-align: left;
            margin-top: 10px;
            padding-left: 20px;
            font-size: 15px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
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
                                                <option value="<?= htmlspecialchars($g['grado']) ?>" <?= $grado == $g['grado'] ? 'selected' : '' ?>>
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
                                                <option value="<?= htmlspecialchars($s['seccion']) ?>" <?= $seccion == $s['seccion'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($s['seccion']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="especialidad" class="form-label">Especialidad</label>
                                        <select class="form-control" id="especialidad" name="especialidad">
                                            <option value="">Todas</option>
                                            <?php while ($esp = $especialidades->fetch_assoc()): ?>
                                                <option value="<?= htmlspecialchars($esp['especialidad']) ?>" <?= $especialidad == $esp['especialidad'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($esp['especialidad']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Filtrar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Grupos de estudiantes -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title text-center">Listado por Grupos</h4>
                                <?php if (!empty($grupos)): ?>
                                    <div class="grid">
                                        <?php foreach ($grupos as $grupoNombre => $estudiantes): ?>
                                            <div class="card">
                                                <div class="card-icon">👥</div>
                                                <div class="card-title"><?= htmlspecialchars($grupoNombre) ?></div>
                                                <div class="card-sub"><?= count($estudiantes) ?> estudiante(s)</div>
                                                <a href="#" class="btn toggle-estudiantes" data-target="<?= md5($grupoNombre) ?>">Ver lista</a>
                                                <div id="<?= md5($grupoNombre) ?>" class="estudiantes-lista" style="display:none;">
                                                    <ul class="estudiantes">
                                                        <?php foreach ($estudiantes as $est): ?>
                                                            <li><?= htmlspecialchars($est['nombre']) ?> (<?= htmlspecialchars($est['grado']) ?><?= htmlspecialchars($est['seccion']) ?><?= $est['especialidad'] ? ', ' . htmlspecialchars($est['especialidad']) : '' ?>)</li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-center">No se encontraron estudiantes.</p>
                                <?php endif; ?>
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
<script>
    document.querySelectorAll('.toggle-estudiantes').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const div = document.getElementById(targetId);
            div.style.display = div.style.display === 'none' ? 'block' : 'none';
        });
    });
</script>
</body>
</html>
