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
    die("Grado y sección no proporcionados.");
}

// Consultar cuántos estudiantes hay en ese grupo
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM estudiantes WHERE grado = ? AND seccion = ?");
$stmt->bind_param("ss", $grado, $seccion);
$stmt->execute();
$total_estudiantes = $stmt->get_result()->fetch_assoc()['total'];

// Consultar libros disponibles
$libros = $conn->query("SELECT id, titulo, autor, stock FROM libros WHERE disponible = 1 AND stock > 0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Libro a <?= htmlspecialchars($grado) ?>° <?= htmlspecialchars($seccion) ?></title>
    <link rel="stylesheet" href="../assets/css/vendor.min.css">
    <link rel="stylesheet" href="../assets/css/app-saas.min.css">
    <link rel="stylesheet" href="../assets/css/icons.min.css">
    <style>
        .container { padding:30px; max-width:700px; margin:auto; }
        .title { text-align:center; margin-bottom:25px; }
        .card { background:#fff; border-radius:8px; padding:25px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
        label { font-weight:600; margin-bottom:6px; display:block; }
        select, button { width:100%; padding:12px; margin-top:8px; border-radius:6px; border:1px solid #ddd; font-size:15px; }
        button { background:#28a745; color:#fff; font-weight:600; cursor:pointer; transition:background 0.3s; }
        button:hover { background:#218838; }
        .btn-back { display:inline-block; margin-bottom:20px; background:#007bff; color:#fff; padding:8px 14px; border-radius:6px; text-decoration:none; font-weight:500; }
        .btn-back:hover { background:#0056b3; }
        .info-box { margin-bottom:20px; padding:12px; border-left:4px solid #007bff; background:#f1f5ff; border-radius:6px; }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="content-page">
        <div class="content">
                <a href="javascript:history.back()" class="btn-back">← Volver</a>
                <h2 class="title">Asignar libro a <?= htmlspecialchars($grado) ?>° <?= htmlspecialchars($seccion) ?></h2>

                <div class="card">
                    <div class="info-box">
                        <strong>Grado:</strong> <?= htmlspecialchars($grado) ?>° <?= htmlspecialchars($seccion) ?><br>
                        <strong>Total estudiantes:</strong> <?= $total_estudiantes ?>
                    </div>

                    <?php if ($total_estudiantes > 0): ?>
                        <form action="procesar_asignacion_grado.php" method="POST">
                            <input type="hidden" name="grado" value="<?= htmlspecialchars($grado) ?>">
                            <input type="hidden" name="seccion" value="<?= htmlspecialchars($seccion) ?>">

                            <label for="libro">Selecciona un libro:</label>
                            <select name="libro_id" id="libro" required>
                                <option value="">-- Selecciona un libro --</option>
                                <?php while($libro = $libros->fetch_assoc()): ?>
                                    <option value="<?= $libro['id'] ?>">
                                        <?= htmlspecialchars($libro['titulo']) ?> - <?= htmlspecialchars($libro['autor']) ?> (Stock: <?= $libro['stock'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>

                            <button type="submit">📚 Asignar a todos</button>
                        </form>
                    <?php else: ?>
                        <p>No hay estudiantes en este grupo.</p>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</div>
</body>
</html>