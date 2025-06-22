<?php
session_start();
// --- DEBUGGING TEMPORAL (¡DESCOMENTA ESTAS LÍNEAS SOLO PARA DEPURAR Y ELIMÍNALAS EN PRODUCCIÓN!) ---
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ----------------------------------------------------------------------------------------------------

$conexion = include_once 'conexion.php'; // Asegúrate de que 'conexion.php' establece la conexión a la base de datos

if (!isset($_GET['id'])) {
    echo "Libro no especificado.";
    exit;
}

$libro_id = intval($_GET['id']);

// Preparamos la consulta SQL para mayor seguridad (evita inyecciones SQL)
$sql = "SELECT l.id, l.titulo, l.autor, l.descripcion, l.tipo_libro, l.archivo_pdf, l.stock, c.nombre AS categoria_nombre
        FROM libros l
        LEFT JOIN categorias_libros c ON l.categoria_id = c.id
        WHERE l.id = ?"; // Usamos un placeholder '?' para el ID

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}
$stmt->bind_param("i", $libro_id); // 'i' indica que $libro_id es un entero
$stmt->execute();
$resultado = $stmt->get_result();

if (!$resultado || $resultado->num_rows === 0) {
    echo "Libro no encontrado.";
    exit;
}

$libro = $resultado->fetch_assoc();

// Asignamos y sanitizamos las variables del libro para mostrar en HTML
$titulo        = htmlspecialchars($libro['titulo'] ?? '');
$autor         = htmlspecialchars($libro['autor'] ?? '');
$descripcion   = htmlspecialchars($libro['descripcion'] ?? '');
$categoria     = htmlspecialchars($libro['categoria_nombre'] ?? '');
$tipo_libro    = htmlspecialchars($libro['tipo_libro'] ?? '');
$archivo_pdf   = htmlspecialchars($libro['archivo_pdf'] ?? ''); // Este es el nombre del archivo (ej. "mi_libro.pdf")
$stock         = intval($libro['stock'] ?? 0);

// --- CONFIGURACIÓN DE RUTAS CLAVE ---
// PASO 1: Define la ruta base URL para tu carpeta 'uploads'.
// Esta es la ruta que el NAVEGADOR usará.
// EJEMPLO: Si tu sitio es 'tudominio.com' y tu proyecto 'ProyectoGrad' está directamente
// en la raíz web (ej. /var/www/html/ProyectoGrad/), entonces la URL para 'uploads' es:
// 'tudominio.com/ProyectoGrad/uploads/'.
// Si tu dominio ya apunta DIRECTAMENTE a la carpeta 'ProyectoGrad', entonces usa '/uploads/'.
$base_url_uploads = '/ProyectoGrad/uploads/'; // <-- ¡AJUSTA ESTA LÍNEA SEGÚN LA ESTRUCTURA DE TU SERVIDOR WEB!

// PASO 2: Define la ruta física ABSOLUTA en el servidor para tu carpeta 'uploads'.
// Esto es lo que PHP usará para la función 'file_exists()'.
// $_SERVER['DOCUMENT_ROOT'] es la raíz física de tu servidor web (ej. /var/www/html).
$base_path_uploads = $_SERVER['DOCUMENT_ROOT'] . $base_url_uploads;

// --- VERIFICACIÓN DE EXISTENCIA DEL ARCHIVO PDF ---
$pdf_path_physical = $base_path_uploads . $archivo_pdf; // Ruta física completa del PDF en el servidor
$pdf_url_browser = $base_url_uploads . $archivo_pdf;     // Ruta URL completa para el enlace del navegador

// Variable booleana para saber si el PDF existe y está disponible
$pdf_exists = ($tipo_libro === 'digital' && !empty($archivo_pdf) && file_exists($pdf_path_physical));

// --- LÍNEAS DE DEPURACIÓN (¡DESCOMENTA ESTAS LÍNEAS TEMPORALMENTE PARA VER LAS RUTAS EN TU NAVEGADOR!) ---
// echo "<p><strong>Ruta URL para el navegador:</strong> " . $pdf_url_browser . "</p>";
// echo "<p><strong>Ruta física en el servidor:</strong> " . $pdf_path_physical . "</p>";
// echo "<p><strong>El archivo PDF existe físicamente:</strong> " . ($pdf_exists ? 'Sí' : 'No') . "</p>";
// ----------------------------------------------------------------------------------------------------------
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
    <style>
     /* Tu CSS personalizado si lo tienes, o déjalo vacío */
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="header-content" style="display:flex; align-items:center; justify-content:space-between;">
            <div class="logo">
                <span><img src="/ProyectoGrad/assets/images/Recurso_23.png" height="50" alt="Logo"></span>
            </div>
            <button class="mobile-menu-btn" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <nav class="nav-menu">
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="catalogo.php"><i class="fas fa-book"></i> Catálogo</a></li>
                    <li><a href="#"><i class="fas fa-bookmark"></i> Mis Libros</a></li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
                    <li><a href="#"><i class="fas fa-info-circle"></i> Acerca de</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> Contacto</a></li>
                    <li><a href="Admin/logout.php" class="login-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<main class="main-content">

    <section class="book-details">
        <div class="book-header">
            <h1 class="book-title"><?php echo $titulo; ?></h1>
        </div>

        <div class="book-content">
            <div class="book-image-section">
                <img src="/placeholder.svg?height=500&width=400" alt="Portada del libro" class="main-image">
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
                                <label for="fecha_devolucion"><strong>Selecciona fecha de devolución:</strong></label><br>
                                <input type="date" name="fecha_devolucion" required min="<?php echo date('Y-m-d'); ?>" style="margin: 10px 0; padding: 6px;"><br>
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