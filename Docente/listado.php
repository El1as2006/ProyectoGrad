<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$grado = $_GET['grado'] ?? '';
$seccion = $_GET['seccion'] ?? '';

if ($grado === '' || $seccion === '') {
    die("Grado y sección son obligatorios.");
}

// Consultar estudiantes del grupo (recomiendo traer también el id para asignar)
$stmt = $conn->prepare("SELECT id, nombre, grado, seccion, especialidad FROM estudiantes WHERE grado = ? AND seccion = ?");
$stmt->bind_param("ss", $grado, $seccion);
$stmt->execute();
$resultado = $stmt->get_result();

$estudiantes = [];
while ($row = $resultado->fetch_assoc()) {
    $estudiantes[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Estudiantes <?= htmlspecialchars($grado) ?>° <?= htmlspecialchars($seccion) ?></title>
    <link rel="stylesheet" href="../assets/css/vendor.min.css">
    <link rel="stylesheet" href="../assets/css/app-saas.min.css">
    <link rel="stylesheet" href="../assets/css/icons.min.css">
    <style>
        .container {
            padding: 30px;
        }

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f1f3f5;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            background: #007bff;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-back:hover {
            background: #0056b3;
        }

        .btn-assign {
            background: #28a745;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-assign:hover {
            background: #218838;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container">
                    <a href="list_grados.php" class="btn-back">← Volver a grupos</a>
                    <div>
                            <a href="asignar_libro_grado.php?grado=<?= urlencode($grado) ?>&seccion=<?= urlencode($seccion) ?>"
                                class="btn-assign">
                                📚 Asignar a todo el grado
                            </a>
                    </div>
                    <h2 class="title">Estudiantes de <?= htmlspecialchars($grado) ?>° <?= htmlspecialchars($seccion) ?>
                    </h2>

                    <?php if (!empty($estudiantes)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Grado</th>
                                    <th>Sección</th>
                                    <th>Especialidad</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $index => $est): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($est['nombre']) ?></td>
                                        <td><?= htmlspecialchars($est['grado']) ?></td>
                                        <td><?= htmlspecialchars($est['seccion']) ?></td>
                                        <td><?= htmlspecialchars($est['especialidad']) ?: '-' ?></td>
                                        <td>
                                            <a href="asignar_libro.php?id=<?= urlencode($est['id']) ?>" class="btn-assign">
                                                📚 Asignar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No se encontraron estudiantes en este grupo.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>