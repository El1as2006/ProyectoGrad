<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$subcategorias = $conn->query("SELECT * FROM subcategorias_libros ORDER BY nombre");

$subcategorias = $conn->query("
    SELECT s.id, s.nombre AS sub_nombre, s.descripcion,
           c.nombre AS categoria_nombre, c.color, c.icono
    FROM subcategorias_libros s
    LEFT JOIN categorias_libros c ON s.categoria_id = c.id
    ORDER BY c.nombre, s.nombre
");


$mensaje = '';
$errores = [];
$nombre = $descripcion = '';
$categoria_id = 0;
$modo = 'agregar'; // agregar, editar


// Procesar parámetros GET para edición
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $categoria_id = (int) $_GET['editar'];
    $modo = 'editar';

    $stmt = $conn->prepare('SELECT nombre, descripcion FROM subcategorias_libros WHERE id = ?');
    $stmt->bind_param('i', $categoria_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($categoria = $result->fetch_assoc()) {
        $nombre = $categoria['nombre'];
        $descripcion = $categoria['descripcion'];
    } else {
        $errores[] = 'Subcategoría no encontrada.';
        $modo = 'agregar';
    }
    $stmt->close();
}

// Procesar eliminación
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $categoria_id_eliminar = (int) $_GET['eliminar'];

    $stmt = $conn->prepare('SELECT COUNT(*) as total FROM libros WHERE categoria_id = ?');
    $stmt->bind_param('i', $categoria_id_eliminar);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_libros = $result->fetch_assoc()['total'];
    $stmt->close();

    if ($total_libros > 0) {
        $errores[] = "No se puede eliminar la subcategoría porque tiene $total_libros libros asociados.";
    } else {
        $stmt = $conn->prepare('DELETE FROM subcategorias_libros WHERE id = ?');
        $stmt->bind_param('i', $categoria_id_eliminar);
        if ($stmt->execute()) {
            $mensaje = 'Subcategoría eliminada correctamente.';
        } else {
            $errores[] = 'Error al eliminar la subcategoría.';
        }
        $stmt->close();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoria_id = (int) ($_POST['categoria_id'] ?? 0);
    $modo = $_POST['modo'] ?? 'agregar';

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }

    if (empty($errores)) {
        if ($modo === 'editar' && $categoria_id > 0) {
            $stmt = $conn->prepare('UPDATE subcategorias_libros SET nombre = ?, descripcion = ? WHERE id = ?');
            $stmt->bind_param('ssi', $nombre, $descripcion, $categoria_id);
            if ($stmt->execute()) {
                $mensaje = 'Subcategoría actualizada correctamente.';
            } else {
                $errores[] = 'Error al actualizar la subcategoría.';
            }
            $stmt->close();
        } else {
            $stmt = $conn->prepare('INSERT INTO subcategorias_libros (nombre, descripcion, categoria_id) VALUES (?, ?, ?)');
            $stmt->bind_param('ssi', $nombre, $descripcion, $categoria_id);

            if ($stmt->execute()) {
                $mensaje = 'Subcategoría agregada correctamente.';
                $nombre = $descripcion = '';
            } else {
                $errores[] = 'Error al guardar la subcategoría.';
            }
            $stmt->close();
        }
    }
}

// Obtener subcategorías
$subcategorias = $conn->query("SELECT * FROM subcategorias_libros ORDER BY nombre");


if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=subcategorias_libros.csv');

    $output = fopen('php://output', 'w');
    // Encabezados de columnas
    fputcsv($output, ['ID Subcategoría', 'Nombre Subcategoría', 'Descripción', 'Nombre Categoría']);

    $queryExport = "
        SELECT 
            s.id AS subcategoria_id,
            s.nombre AS subcategoria_nombre,
            s.descripcion,
            c.nombre AS categoria_nombre
        FROM subcategorias_libros s
        LEFT JOIN categorias_libros c ON s.categoria_id = c.id
        ORDER BY c.nombre, s.nombre
    ";

    $resultExport = $conn->query($queryExport);

    if ($resultExport && $resultExport->num_rows > 0) {
        while ($row = $resultExport->fetch_assoc()) {
            fputcsv($output, [
                $row['subcategoria_id'],
                $row['subcategoria_nombre'],
                $row['descripcion'],
                $row['categoria_nombre']
            ]);
        }
    }
    fclose($output);
    exit;
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Listado de Categorías y Subcategorías</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row">

                        <!-- Formulario más pequeño -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title"><?= $modo === 'editar' ? 'Editar' : 'Agregar' ?> Subcategoría
                                    </h4>

                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-success"><?= $mensaje ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($errores)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errores as $error): ?>
                                                    <li><?= htmlspecialchars($error) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <form method="post">
                                        <input type="hidden" name="categoria_id" value="<?= $categoria_id ?>">
                                        <input type="hidden" name="modo" value="<?= $modo ?>">

                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                value="<?= htmlspecialchars($nombre) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion"
                                                name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="categoria_id" class="form-label">Categoría</label>
                                            <select class="form-control" id="categoria_id" name="categoria_id" required>
                                                <option value="">Seleccione una categoría</option>

                                                <?php
                                                $categorias = $conn->query("SELECT id, nombre FROM categorias_libros ORDER BY nombre");
                                                while ($cat = $categorias->fetch_assoc()):
                                                    ?>
                                                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $categoria_id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cat['nombre']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>


                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla más ancha -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Subcategorías de Libros</h4>
                                    <a href="?export=excel" class="btn btn-success mb-3">Exportar a Excel</a>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nombre de la categoria</th>
                                                    <th>Subcategorías</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                $query = "
    SELECT 
        c.id AS categoria_id,
        c.nombre AS categoria_nombre,
        c.color,
        c.icono,
        GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR ', ') AS subcategorias
    FROM categorias_libros c
    LEFT JOIN subcategorias_libros s ON s.categoria_id = c.id
    GROUP BY c.id, c.nombre, c.color, c.icono
    ORDER BY c.nombre
";





                                                $result = $conn->query($query);

                                                if (!$result) {
                                                    echo "Error en la consulta SQL: " . $conn->error;
                                                } else {
                                                    echo "Filas encontradas: " . $result->num_rows . "<br>";
                                                }

                                                if ($result && $result->num_rows > 0):
                                                    while ($row = $result->fetch_assoc()):
                                                        ?>
                                                        <tr class="categoria-row"
                                                            data-subcategorias="<?= htmlspecialchars($row['subcategorias'] ?? 'Sin subcategorías') ?>">
                                                            <td>
                                                                <span class="badge"
                                                                    style="background-color: <?= htmlspecialchars($row['color']) ?>;">
                                                                    <i class="<?= htmlspecialchars($row['icono']) ?>"></i>
                                                                    <?= htmlspecialchars($row['categoria_nombre']) ?>
                                                                </span>
                                                            </td>
                                                            <td class="subcategoria-cell">
                                                                <button class="btn btn-sm btn-info show-subcategorias">Ver
                                                                    subcategorías</button>
                                                                <div class="subcategoria-list mt-2" style="display:none;"></div>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    endwhile;
                                                else:
                                                    ?>
                                                    <tr>
                                                        <td colspan="2" class="text-center">No hay datos disponibles.</td>
                                                    </tr>
                                                    <?php
                                                endif;
                                                ?>



                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const botones = document.querySelectorAll('.show-subcategorias');

                            botones.forEach(function (boton) {
                                boton.addEventListener('click', function () {
                                    const row = boton.closest('.categoria-row');
                                    const subcategorias = row.getAttribute('data-subcategorias');
                                    const listDiv = row.querySelector('.subcategoria-list');

                                    if (listDiv.style.display === 'none') {
                                        listDiv.style.display = 'block';
                                        listDiv.innerHTML = subcategorias.split(', ').map(function (sub) {
                                            return `<div>- ${sub}</div>`;
                                        }).join('');
                                    } else {
                                        listDiv.style.display = 'none';
                                        listDiv.innerHTML = '';
                                    }
                                });
                            });
                        });
                    </script>

                    <script src="../assets/js/vendor.min.js"></script>
                    <script src="../assets/js/app.min.js"></script>
</body>

</html>