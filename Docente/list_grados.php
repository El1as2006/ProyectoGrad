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

// Agrupar por grado y sección (ej: 5° B)
$grupos = [];
while ($row = $resultado->fetch_assoc()) {
    $grupoClave = "{$row['grado']}° {$row['seccion']}";
    $claveLink = "{$row['grado']}|{$row['seccion']}";
    $grupos[$grupoClave]['estudiantes'][] = $row;
    $grupos[$grupoClave]['grado'] = $row['grado'];
    $grupos[$grupoClave]['seccion'] = $row['seccion'];
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
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
        }

        .card a {
            text-decoration: none;
            color: inherit;
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
                        <div class="col-md-12"> 
                            <div class="card-body">
                                <h4 class="card-title text-center">Listado por Grado y Sección</h4>
                                <?php if (!empty($grupos)): ?>
                                    <div class="grid">
                                        <?php foreach ($grupos as $nombreGrupo => $datos): ?>
                                            <?php
                                            $link = "listado.php?grado=" . urlencode($datos['grado']) . "&seccion=" . urlencode($datos['seccion']);
                                            $total = count($datos['estudiantes']);
                                            ?>
                                            <a href="<?= $link ?>">
                                                <div class="card">
                                                    <div class="card-icon">👥</div>
                                                    <div class="card-title"><?= htmlspecialchars($nombreGrupo) ?></div>
                                                    <div class="card-sub"><?= $total ?> estudiante(s)</div>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-center">No se encontraron estudiantes.</p>
                                <?php endif; ?>
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