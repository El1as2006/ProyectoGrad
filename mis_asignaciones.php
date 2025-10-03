<?php
session_start();
$conexion = include_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Admin/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nombre = $_SESSION['user_name'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'estudiante';

// Obtener préstamos del usuario con status = 'asignacion'
$query = "SELECT p.*, l.titulo, l.autor 
          FROM prestamos p
          JOIN libros l ON p.id_libro = l.id
          WHERE p.id_usuario = ? 
          AND p.status = 'asignacion'
          ORDER BY p.fecha_prestamo DESC";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$resultado = $stmt->get_result();
$prestamos = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Libros Asignados – Biblioteca Chaleca</title>
    <link rel="stylesheet" href="/ProyectoGrad/assets/css/indexstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        main.container {
            padding: 20px;
        }

        .prestamo-lista {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .prestamo-lista th,
        .prestamo-lista td {
            padding: 12px 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .prestamo-lista th {
            background-color: #f2f2f2;
        }

        .qr-img {
            width: 60px;
            height: 60px;
        }

        h2.section-title {
            text-align: center;
            font-size: 26px;
            margin-bottom: 20px;
        }

        .status-pendiente {
            color: orange;
        }

        .status-devuelto {
            color: green;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; // si tienes header separado ?>
    <main class="container">
        <h2 class="section-title">Libros Asignados</h2>

        <?php if (empty($prestamos)): ?>
            <p>No tienes libros asignados.</p>
        <?php else: ?>
            <table class="prestamo-lista">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Fecha Préstamo</th>
                        <th>Fecha Devolución</th>
                        <th>Estado</th>
                        <th>QR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prestamos as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= htmlspecialchars($p['autor']) ?></td>
                            <td><?= htmlspecialchars($p['fecha_prestamo']) ?></td>
                            <td><?= $p['fecha_devolucion'] ? htmlspecialchars($p['fecha_devolucion']) : '—' ?></td>
                            <td class="<?= $p['status'] === 'devuelto' ? 'status-devuelto' : 'status-pendiente' ?>">
                                <?= ucfirst($p['status']) ?>
                            </td>
                            <td>
                                <?php if (!empty($p['qr_prestamo'])): ?>
                                    <img src="<?= htmlspecialchars($p['qr_prestamo']) ?>" alt="QR" class="qr-img">
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>