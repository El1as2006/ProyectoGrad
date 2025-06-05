<?php
include '../conexion.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: list_books.php');
    exit;
}

$mensaje = '';
$errores = [];

// Obtener datos actuales
$stmt = $conn->prepare('SELECT titulo, autor, genero, tipo_libro, año_publicacion, isbn, descripcion, disponible, archivo_pdf FROM libros WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    header('Location: list_books.php');
    exit;
}
$stmt->bind_result($titulo, $autor, $genero, $tipo_libro, $anio_publicacion, $isbn, $descripcion, $disponible, $archivo_pdf);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $tipo_libro = trim($_POST['tipo_libro'] ?? '');
    $anio_publicacion = trim($_POST['año_publicacion'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    $nuevo_pdf = $archivo_pdf;

    if ($titulo === '') { $errores[] = 'El título es obligatorio.'; }
    if ($autor === '') { $errores[] = 'El autor es obligatorio.'; }
    if ($genero === '') { $errores[] = 'El género es obligatorio.'; }
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
        $stmt = $conn->prepare('UPDATE libros SET titulo=?, autor=?, genero=?, tipo_libro=?, año_publicacion=?, isbn=?, descripcion=?, disponible=?, archivo_pdf=? WHERE id=?');
        $stmt->bind_param('sssssssisi', $titulo, $autor, $genero, $tipo_libro, $anio_publicacion, $isbn, $descripcion, $disponible, $nuevo_pdf, $id);
        if ($stmt->execute()) {
            $mensaje = 'Libro actualizado correctamente.';
            $archivo_pdf = $nuevo_pdf;
        } else {
            $errores[] = 'Error al actualizar en la base de datos.';
        }
        $stmt->close();
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
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
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
                                            <label for="tipo_libro" class="form-label">Tipo de Libro</label>
                                            <select class="form-control" id="tipo_libro" name="tipo_libro" required>
                                                <option value="">Seleccione...</option>
                                                <option value="digital" <?= $tipo_libro === 'digital' ? 'selected' : '' ?>>Digital</option>
                                                <option value="fisico" <?= $tipo_libro === 'fisico' ? 'selected' : '' ?>>Físico</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="año_publicacion" class="form-label">Año de Publicación</label>
                                            <input type="number" class="form-control" id="año_publicacion" name="año_publicacion" value="<?= htmlspecialchars($anio_publicacion) ?>" required min="1000" max="9999">
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
</body>
</html>
