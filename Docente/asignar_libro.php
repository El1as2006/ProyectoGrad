<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$estudiante_id = $_GET['id'] ?? '';

if ($estudiante_id === '') {
    die("ID de estudiante no proporcionado.");
}

// Consultar datos del estudiante
$stmt = $conn->prepare("SELECT id, nombre, grado, seccion, especialidad FROM estudiantes WHERE id = ?");
$stmt->bind_param("i", $estudiante_id);
$stmt->execute();
$estudiante = $stmt->get_result()->fetch_assoc();

if (!$estudiante) {
    die("Estudiante no encontrado.");
}

// Consultar libros disponibles
$libros = $conn->query("SELECT id, titulo, autor, stock FROM libros WHERE disponible = 1 AND stock > 0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Libro</title>
    <link rel="stylesheet" href="../assets/css/vendor.min.css">
    <link rel="stylesheet" href="../assets/css/app-saas.min.css">
    <link rel="stylesheet" href="../assets/css/icons.min.css">
    <style>
        .container {
            padding: 30px;
            max-width: 700px;
            margin: auto;
        }
        .title {
            text-align: center;
            margin-bottom: 25px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }
        select, button {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 15px;
        }
        button {
            background: #28a745;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #218838;
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
        .info-box {
            margin-bottom: 20px;
            padding: 12px;
            border-left: 4px solid #007bff;
            background: #f1f5ff;
            border-radius: 6px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="content-page">
        <div class="content">
            <div class="container">
                <a href="javascript:history.back()" class="btn-back">← Volver</a>
                <h2 class="title">Asignar libro a estudiante</h2>

                <div class="card">
                    <div class="info-box">
                        <strong>Estudiante:</strong> <?= htmlspecialchars($estudiante['nombre']) ?><br>
                        <strong>Grado:</strong> <?= htmlspecialchars($estudiante['grado']) ?>° <?= htmlspecialchars($estudiante['seccion']) ?><br>
                        <strong>Especialidad:</strong> <?= htmlspecialchars($estudiante['especialidad']) ?: '-' ?>
                    </div>

                    <form action="procesar_asignacion.php" method="POST">
                        <input type="hidden" name="estudiante_id" value="<?= $estudiante['id'] ?>">

                        <label for="libro">Selecciona un libro:</label>
                        <select name="libro_id" id="libro" required>
                            <option value="">-- Selecciona un libro --</option>
                            <?php while($libro = $libros->fetch_assoc()): ?>
                                <option value="<?= $libro['id'] ?>">
                                    <?= htmlspecialchars($libro['titulo']) ?> - <?= htmlspecialchars($libro['autor']) ?> (Stock: <?= $libro['stock'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>

                        <button type="submit">📚 Asignar Libro</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
