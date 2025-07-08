<?php
session_start();
// --- DEBUGGING TEMPORAL (¡DESCOMENTA ESTAS LÍNEAS SOLO PARA DEPURAR Y ELIMINAR EN PRODUCCIÓN!) ---
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ----------------------------------------------------------------------------------------------------

$conexion = include_once 'conexion.php'; // Asegúrate de que 'conexion.php' establece la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libro_id = intval($_POST['libro_id'] ?? 0);
    $fecha_devolucion = $_POST['fecha_devolucion'] ?? '';

    $fecha_hoy = date('Y-m-d');
    $fecha_limite = date('Y-m-d', strtotime('+30 days'));

    // Validación segura de fecha
    if (empty($fecha_devolucion) || $fecha_devolucion < $fecha_hoy || $fecha_devolucion > $fecha_limite) {
        die('Error: La fecha de devolución no es válida. Debe ser entre hoy y 30 días máximo.');
    }
}

if (!isset($_GET['id'])) {
    echo "Libro no especificado.";
    exit;
}

$libro_id = intval($_GET['id']);

// Preparamos la consulta SQL para mayor seguridad (evita inyecciones SQL)
$sql = "SELECT l.id, l.titulo, l.autor, l.descripcion, l.tipo_libro, l.archivo_pdf, l.imagen, l.stock, c.nombre AS categoria_nombre
        FROM libros l
        LEFT JOIN categorias_libros c ON l.categoria_id = c.id
        WHERE l.id = ?";
 

// Ejemplo para estudiante:
$stmt = $conn->prepare("INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, status, origen_usuario)
                        VALUES (?, ?, ?, ?, ?, 'estudiante')");
$stmt->bind_param("iisss", $id_estudiante, $id_libro, $fecha_prestamo, $fecha_devolucion, $status);

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}
$stmt->bind_param("i", $libro_id);
$stmt->execute();
$resultado = $stmt->get_result();

if (!$resultado || $resultado->num_rows === 0) {
    echo "Libro no encontrado.";
    exit;
}

$libro = $resultado->fetch_assoc();


// Sanitizamos variables para mostrar en HTML
$titulo = htmlspecialchars($libro['titulo'] ?? '');
$autor = htmlspecialchars($libro['autor'] ?? '');
$descripcion = htmlspecialchars($libro['descripcion'] ?? '');
$categoria = htmlspecialchars($libro['categoria_nombre'] ?? '');
$tipo_libro = htmlspecialchars($libro['tipo_libro'] ?? '');
$archivo_pdf = htmlspecialchars($libro['archivo_pdf'] ?? '');



// --- CORRECCIÓN: Eliminamos prefijo 'uploads/' si existe para evitar ruta duplicada ---
$archivo_pdf = preg_replace('#^uploads/#', '', $archivo_pdf);

$stock = intval($libro['stock'] ?? 0);

// --- CONFIGURACIÓN DE RUTAS ---

// Ruta URL base para la carpeta uploads (ajusta según tu estructura)
$base_url_uploads = '/ProyectoGrad/uploads/';

// Ruta física absoluta para la carpeta uploads (servidor)
$base_path_uploads = $_SERVER['DOCUMENT_ROOT'] . $base_url_uploads;

// Ruta física completa del PDF en servidor
$pdf_path_physical = $base_path_uploads . $archivo_pdf;

// Ruta URL completa para navegador
$pdf_url_browser = $base_url_uploads . $archivo_pdf;

// Comprobamos si archivo existe para libros digitales
$pdf_exists = ($tipo_libro === 'digital' && !empty($archivo_pdf) && file_exists($pdf_path_physical));

// --- DEPURACIÓN TEMPORAL (descomenta para verificar rutas) ---
// echo "<p>Ruta URL para navegador: $pdf_url_browser</p>";
// echo "<p>Ruta física en servidor: $pdf_path_physical</p>";
// echo "<p>¿Archivo existe?: " . ($pdf_exists ? 'Sí' : 'No') . "</p>";
// --------------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalles del Libro - <?php echo $titulo; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ProyectoGrad/assets/css/indexstyle.css">
    <link rel="stylesheet" href="/ProyectoGrad/assets/css/librodetalles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header>
        <?php include 'header.php'; ?>

    </header>

    <main class="main-content">

        <section class="book-details">
            <div class="book-header">
                <h1 class="book-title"><?php echo $titulo; ?></h1>
            </div>

            <div class="book-content">
                <div class="book-image-section">
                    <?php
                    $imagen = htmlspecialchars($libro['imagen'] ?? '');
                    $imagen = !empty($imagen) ? "/ProyectoGrad/assets/book/$imagen" : "/ProyectoGrad/assets/book/img_libros.jpg";
                    ?>

                    <img src="<?php echo $imagen; ?>" alt="Portada del libro" class="main-image">

                </div>

                <div class="book-info">
                    <?php if (isset($_GET['prestamo']) && $_GET['prestamo'] === 'ok'): ?>
                        <p style="color: green;"><strong>¡Préstamo realizado correctamente!</strong></p>
                    <?php endif; ?>

                    <div class="availability">
                        <?php echo $tipo_libro === 'digital' ? 'Disponible en línea' : 'Disponible en biblioteca'; ?>
                    </div>

                    <div class="book-actions">
                        <?php if ($tipo_libro === 'digital'): ?>
                            <?php if ($pdf_exists): ?>
                                <a href="<?php echo $pdf_url_browser; ?>" target="_blank" class="btn-primary">📖 Leer libro</a>
                            <?php else: ?>
                                <p style="color:red;">Archivo no disponible para este libro.</p>
                            <?php endif; ?>
                        <?php elseif ($tipo_libro === 'fisico'): ?>
                            <?php if ($stock > 0): ?>
                                <form action="pedir_libro.php" method="POST">
                                    <input type="hidden" name="libro_id" value="<?php echo $libro_id; ?>">
                                    <label for="fecha_devolucion"><strong>Selecciona fecha de devolución (máx. 30
                                            días):</strong></label><br>
                                    <input type="date" name="fecha_devolucion" required min="<?php echo date('Y-m-d'); ?>"
                                        max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>"
                                        style="margin: 10px 0; padding: 6px;"><br>
                                    <button type="submit" class="btn-primary">📚 Pedir préstamo</button>
                                </form>
                            <?php else: ?>
                                <p style="color:red;"><strong>No disponible para préstamo. Sin stock.</strong></p>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>

                    <div style="margin-top: 20px;">
                        <h3>Descripción:</h3>
                        <p><?php echo nl2br($descripcion); ?></p>
                    </div>

                    <div class="info-grid">
                        <div class="info-item"><strong>Autor:</strong> <?php echo $autor; ?></div>
                        <div class="info-item"><strong>Categoría:</strong> <?php echo $categoria; ?></div>
                        <div class="info-item"><strong>Tipo:</strong> <?php echo ucfirst($tipo_libro); ?></div>
                        <div class="info-item"><strong>Stock:</strong> <?php echo $stock; ?></div>
                    </div>

                    <div style="margin-top: 20px;">
                        <a href="catalogo.php" class="btn-secondary">← Volver al catálogo</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.querySelector('.mobile-menu-btn').addEventListener('click', function () {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        document.addEventListener('click', function (event) {
            const isClickInsideNav = event.target.closest('.nav-menu');
            const isClickOnMenuBtn = event.target.closest('.mobile-menu-btn');
            if (!isClickInsideNav && !isClickOnMenuBtn && document.querySelector('.nav-menu').classList.contains('active')) {
                document.querySelector('.nav-menu').classList.remove('active');
            }
        });
    </script>
</body>

</html>