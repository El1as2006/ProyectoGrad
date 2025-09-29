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
$sql = "SELECT l.id, l.titulo, l.autor, l.descripcion, l.tipo_libro, l.archivo_pdf, l.stock, c.nombre AS categoria_nombre
        FROM libros l
        LEFT JOIN categorias_libros c ON l.categoria_id = c.id
        WHERE l.id = ?";


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



// --- CORRECCIÓN: Eliminamos prefijo 'uploads/' si existe para evitar ruta duplicada (casing agnóstico) ---
$archivo_pdf = preg_replace('#^(?i)uploads/#', '', $archivo_pdf);

$stock = intval($libro['stock'] ?? 0);

// --- CONFIGURACIÓN DE RUTAS ---

// Ruta URL base para la carpeta Uploads (ajusta según tu estructura)
$base_url_uploads = '/ProyectoGrad/Uploads/';

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
    <link rel="stylesheet" href="/ProyectoGrad-Logica-ProyectoGrad/assets/css/indexstyle.css">
    <link rel="stylesheet" href="/ProyectoGrad-Logica-ProyectoGrad/assets/css/librodetalles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header>
        <?php include_once 'header.php'; ?>

    </header>

    <main class="main-content">

        <section class="book-details">
            <div class="book-header">
                <h1 class="book-title"><?php echo $titulo; ?></h1>
            </div>

            <div class="book-content">
                <div class="book-image-section">
                    <?php
                    // Imagen de portada (DB no almacena columna 'imagen' en el esquema actual)
                    $imagen = "/ProyectoGrad-Logica-ProyectoGrad/assets/book/img_libros.jpg";
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
                                <button type="button" class="btn-primary"
                                    onclick="openPdfModal('<?php echo $pdf_url_browser; ?>')">📖 Leer libro</button>
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


    <!-- DISEÑO DE LEER LIBRO -->
    <!-- Botón --> <button onclick="openPdfModal('tu-archivo.pdf')">📖 Leer libro</button> <!-- Modal -->

    <div id="pdfModal" class="modal">
    <div class="modal-content">
        <button class="close-btn" onclick="closePdfModal()">✕</button>

        <!-- Contenedor del libro -->
        <div class="book-frame">
            <button class="nav-btn left" onclick="prevPage()" aria-label="Página anterior">
                ‹
            </button>
            <div id="book-container"></div>
            <button class="nav-btn right" onclick="nextPage()" aria-label="Página siguiente">
                ›
            </button>
        </div>
    </div>
</div>

    <style>
        .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.85);
    align-items: center;
    justify-content: center;
    padding: 0 20px;
}

/* Contenido */
.modal-content {
    position: relative;
    width: 100%;
    max-width: 95vw;
    height: 90vh;
    background: #fefefe;
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    padding: 15px;
}

/* Botón de cerrar */
.close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    background: transparent;
    border: none;
    font-size: 30px;
    color: #333;
    cursor: pointer;
    z-index: 10;
}

/* Marco del libro */
.book-frame {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eae7dc;
    padding: 20px;
    border-radius: 10px;
    height: 100%;
    overflow: hidden;
    position: relative;
}

/* Contenedor de las páginas */
#book-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    flex-wrap: nowrap;
    overflow: hidden;
    height: 100%;
    max-width: 90%;
}

/* Página del PDF */
.pdf-page {
    max-height: 100%;
    max-width: 48%;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    border-radius: 5px;
}

/* Botones navegación minimalistas */
.nav-btn {
    background: none;
    border: none;
    color: #444;
    font-size: 40px;
    padding: 10px;
    cursor: pointer;
    user-select: none;
    transition: color 0.3s;
    z-index: 5;
}

.nav-btn:hover {
    color: #000;
}
    </style>

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

    <script>
        let pdfDoc = null; let currentPage = 1; const container = document.getElementById("book-container"); async function openPdfModal(url) { document.getElementById("pdfModal").style.display = "flex"; pdfDoc = await pdfjsLib.getDocument(url).promise; currentPage = 1; renderBook(); } function closePdfModal() { document.getElementById("pdfModal").style.display = "none"; container.innerHTML = ""; } async function renderBook() {
            container.innerHTML = ""; for (let i = 0; i < 2; i++) {
                const pageNum = currentPage + i; if (pageNum <= pdfDoc.numPages) {
                    const page = await pdfDoc.getPage(pageNum); const viewport = page.getViewport({ scale: 1.5 });
                    const canvas = document.createElement("canvas"); canvas.classList.add("pdf-page"); const ctx = canvas.getContext("2d"); canvas.width = viewport.width; canvas.height = viewport.height; await page.render({ canvasContext: ctx, viewport: viewport }).promise; container.appendChild(canvas);
                }
            }
        } function nextPage() { if (currentPage + 2 <= pdfDoc.numPages) { currentPage += 2; renderBook(); } } function prevPage() { if (currentPage - 2 >= 1) { currentPage -= 2; renderBook(); } }

    </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

<script>
    let pdfDoc = null;
    let currentPage = 1;
    const container = document.getElementById("book-container");

    async function openPdfModal(url) {
        document.getElementById("pdfModal").style.display = "flex";
        pdfDoc = await pdfjsLib.getDocument(url).promise;
        currentPage = 1;
        renderBook();
    }

    function closePdfModal() {
        document.getElementById("pdfModal").style.display = "none";
        container.innerHTML = "";
    }

    async function renderBook() {
        container.innerHTML = "";
        for (let i = 0; i < 2; i++) {
            const pageNum = currentPage + i;
            if (pageNum <= pdfDoc.numPages) {
                const page = await pdfDoc.getPage(pageNum);
                const viewport = page.getViewport({ scale: 1.2 });
                const canvas = document.createElement("canvas");
                canvas.classList.add("pdf-page");
                const ctx = canvas.getContext("2d");
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                container.appendChild(canvas);
            }
        }
    }

    function nextPage() {
        if (currentPage + 2 <= pdfDoc.numPages) {
            currentPage += 2;
            renderBook();
        }
    }

    function prevPage() {
        if (currentPage - 2 >= 1) {
            currentPage -= 2;
            renderBook();
        }
    }
</script>

    <!-- DISEÑO DE LEER LIBRO -->


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