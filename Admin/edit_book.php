<?php
include '../conexion.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: list_books.php');
    exit;
}

$mensaje = '';
$errores = [];

// Obtener categorías disponibles
$categorias = [];
$result = $conn->query('SELECT id, nombre, color, icono FROM categorias_libros WHERE activo = 1 ORDER BY nombre');
if ($result) {
    $categorias = $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener datos actuales
$stmt = $conn->prepare('SELECT titulo, autor, genero, categoria_id, tipo_libro, anio_publicacion, isbn, descripcion, disponible, archivo_pdf FROM libros WHERE id = ?');
if (!$stmt) {
    die('<div class="alert alert-danger">Error en la consulta SQL: ' . $conn->error . '</div>');
}
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    header('Location: list_books.php');
    exit;
}
$stmt->bind_result($titulo, $autor, $genero, $categoria_id, $tipo_libro, $anio_publicacion, $isbn, $descripcion, $disponible, $archivo_pdf);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $tipo_libro = trim($_POST['tipo_libro'] ?? '');
    $anio_publicacion = trim($_POST['anio_publicacion'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    $nuevo_pdf = $archivo_pdf;

    if ($titulo === '') { $errores[] = 'El título es obligatorio.'; }
    if ($autor === '') { $errores[] = 'El autor es obligatorio.'; }
    if ($genero === '') { $errores[] = 'El género es obligatorio.'; }
    if ($categoria_id === 0) { $errores[] = 'La categoría es obligatoria.'; }
    if ($tipo_libro === '') { $errores[] = 'El tipo de libro es obligatorio.'; }
    if (!preg_match('/^\d{4}$/', $anio_publicacion)) { $errores[] = 'Año inválido.'; }
    if ($isbn === '') { $errores[] = 'El ISBN es obligatorio.'; }
    if ($descripcion === '') { $errores[] = 'La descripción es obligatoria.'; }

    if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['archivo_pdf']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $errores[] = 'El archivo debe ser un PDF.';
        } else {
            $nombre_archivo = uniqid('libro_', true) . '.pdf';
            $ruta_destino = '../uploads/' . $nombre_archivo;
            if (move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $ruta_destino)) {
                $nuevo_pdf = 'uploads/' . $nombre_archivo;
            } else {
                $errores[] = 'Error al subir el archivo PDF.';
            }
        }
    }

    if (empty($errores)) {
        $stmt = $conn->prepare('UPDATE libros SET titulo=?, autor=?, genero=?, categoria_id=?, tipo_libro=?, anio_publicacion=?, isbn=?, descripcion=?, disponible=?, archivo_pdf=? WHERE id=?');
        if (!$stmt) {
            $errores[] = 'Error en la consulta SQL: ' . $conn->error;
        } else {
            $stmt->bind_param('sssississsi', $titulo, $autor, $genero, $categoria_id, $tipo_libro, $anio_publicacion, $isbn, $descripcion, $disponible, $nuevo_pdf, $id);
            if ($stmt->execute()) {
                $mensaje = 'Libro actualizado correctamente.';
                $archivo_pdf = $nuevo_pdf;
            } else {
                $errores[] = 'Error al actualizar en la base de datos.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Editar Libro | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/material-icons-fix.css" rel="stylesheet" type="text/css" />
    <style>
        .categoria-option {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
        }
        .categoria-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            color: white;
            font-size: 0.875rem;
            margin-right: 0.5rem;
        }
        .categoria-badge i {
            margin-right: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        include 'includes/session_check.php';
        include 'includes/sidebar.php';
        ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card mt-4">
                                <div class="card-header bg-warning text-dark">
                                    <h4 class="mb-0">Editar Libro</h4>
                                </div>
                                <div class="card-body">
                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-success"> <?= $mensaje ?> </div>
                                    <?php endif; ?>
                                    <?php if ($errores): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errores as $e) { echo "<li>$e</li>"; } ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post" enctype="multipart/form-data" novalidate>
                                        <div class="mb-3">
                                            <label for="titulo" class="form-label">Título</label>
                                            <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="autor" class="form-label">Autor</label>
                                            <input type="text" class="form-control" id="autor" name="autor" value="<?= htmlspecialchars($autor) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="genero" class="form-label">Género</label>
                                            <input type="text" class="form-control" id="genero" name="genero" value="<?= htmlspecialchars($genero) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="categoria_id" class="form-label">Categoría *</label>
                                            <select class="form-control" id="categoria_id" name="categoria_id" required>
                                                <option value="">Seleccione una categoría...</option>
                                                <?php foreach ($categorias as $categoria): ?>
                                                    <option value="<?= $categoria['id'] ?>" 
                                                            <?= $categoria_id == $categoria['id'] ? 'selected' : '' ?>
                                                            data-color="<?= $categoria['color'] ?>"
                                                            data-icono="<?= $categoria['icono'] ?>">
                                                        <?= htmlspecialchars($categoria['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div id="categoria-preview" class="mt-2" style="display: none;">
                                                <span class="categoria-badge">
                                                    <i></i>
                                                    <span></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tipo_libro" class="form-label">Tipo de Libro</label>
                                            <select class="form-control" id="tipo_libro" name="tipo_libro" required>
                                                <option value="">Seleccione...</option>
                                                <option value="digital" <?= $tipo_libro === 'digital' ? 'selected' : '' ?>>Digital</option>
                                                <option value="fisico" <?= $tipo_libro === 'fisico' ? 'selected' : '' ?>>Físico</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="anio_publicacion" class="form-label">Año de Publicación</label>
                                            <input type="number" class="form-control" id="anio_publicacion" name="anio_publicacion" value="<?= htmlspecialchars($anio_publicacion) ?>" required min="1000" max="9999">
                                        </div>
                                        <div class="mb-3">
                                            <label for="isbn" class="form-label">ISBN</label>
                                            <input type="text" class="form-control" id="isbn" name="isbn" value="<?= htmlspecialchars($isbn) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" required><?= htmlspecialchars($descripcion) ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="archivo_pdf" class="form-label">Archivo PDF</label>
                                            <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept=".pdf">
                                            <?php if ($archivo_pdf): ?>
                                                <p class="mt-2">PDF actual: <a href="../<?= htmlspecialchars($archivo_pdf) ?>" target="_blank">Ver PDF</a></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="disponible" name="disponible" value="1" <?= $disponible ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="disponible">Disponible</label>
                                        </div>
                                        <button type="submit" class="btn btn-warning">Actualizar</button>
                                        <a href="list_books.php" class="btn btn-secondary ms-2">Volver</a>
                                    </form>
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
    <script src="includes/notifications.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoriaSelect = document.getElementById('categoria_id');
            const categoriaPreview = document.getElementById('categoria-preview');
            
            categoriaSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.value) {
                    const color = selectedOption.getAttribute('data-color');
                    const icono = selectedOption.getAttribute('data-icono');
                    const nombre = selectedOption.text;
                    
                    categoriaPreview.style.display = 'block';
                    const badge = categoriaPreview.querySelector('.categoria-badge');
                    badge.style.backgroundColor = color;
                    badge.querySelector('i').className = icono;
                    badge.querySelector('span').textContent = nombre;
                } else {
                    categoriaPreview.style.display = 'none';
                }
            });
            
            // Trigger change event if there's a pre-selected category
            if (categoriaSelect.value) {
                categoriaSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
