<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include '../conexion.php';

$id_usuario = $_SESSION['user_id'] ?? null;

// Consulta principal para la tabla de libros con categorías
$libros = [];
$result = $conn->query("
    SELECT l.*, 
           cl.nombre as categoria_nombre, 
           cl.color as categoria_color, 
           cl.icono as categoria_icono
    FROM libros l
    LEFT JOIN categorias_libros cl ON l.categoria_id = cl.id
    ORDER BY l.id DESC
");
if ($result === false) {
    echo '<div class="alert alert-danger">Error en la consulta SQL: ' . $conn->error . '</div>';
} else {
    while($row = $result->fetch_assoc()) {
        $libros[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Listado de Libros | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/material-icons-fix.css" rel="stylesheet" type="text/css" />
    <!-- PDF.js y animaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/session_check.php'; ?>
        <?php include 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Listado de Libros</h4>
                                    <a href="add_book.php" class="btn btn-light btn-sm">+ Añadir Libro</a>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_GET['deleted'])): ?>
                                        <div class="alert alert-success">Libro eliminado correctamente.</div>
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Título</th>
                                                    <th>Autor</th>
                                                    <th>Género</th>
                                                    <th>Categoría</th>
                                                    <th>Tipo</th>
                                                    <th>Año</th>
                                                    <th>ISBN</th>
                                                    <th>Descripción</th>
                                                    <th>PDF</th>
                                                    <th>Disponible</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($libros as $row): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                                                    <td><?= htmlspecialchars($row['autor']) ?></td>
                                                    <td><?= htmlspecialchars($row['genero']) ?></td>
                                                    <td>
                                                        <?php if ($row['categoria_nombre']): ?>
                                                            <span class="badge d-inline-flex align-items-center gap-1" 
                                                                  style="background-color: <?= $row['categoria_color'] ?>; color: white;">
                                                                <i class="<?= $row['categoria_icono'] ?>"></i>
                                                                <?= htmlspecialchars($row['categoria_nombre']) ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Sin categoría</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['tipo_libro']) ?></td>
                                                    <td><?= htmlspecialchars($row['anio_publicacion']) ?></td>
                                                    <td><?= htmlspecialchars($row['isbn']) ?></td>
                                                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                                    <td>
                                                        <?php if ($row['archivo_pdf']): ?>
                                                            <button type="button" class="btn btn-outline-primary btn-sm" title="Ver PDF" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $row['id'] ?>">
                                                                <i class="mdi mdi-file-pdf"></i> PDF
                                                            </button>
                                                            <!-- Modal para visor PDF avanzado -->
                                                            <div class="modal fade" id="pdfModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="pdfModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                                                <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:900px;">
                                                                    <div class="modal-content rounded-4 shadow-lg border-0 animate__animated animate__fadeIn animate__faster">
                                                                        <div class="modal-header bg-primary text-white rounded-top-4" style="border-bottom: 2px solid #fff2;">
                                                                            <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="pdfModalLabel<?= $row['id'] ?>">
                                                                                <i class="mdi mdi-book-open-page-variant"></i> Ver PDF: <span><?= htmlspecialchars($row['titulo']) ?></span>
                                                                            </h5>
                                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                        </div>
                                                                        <div class="modal-body p-0 animate__animated animate__fadeInUp animate__faster" style="background:#181a1b; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:70vh;">
                                                                            <div class="w-100 d-flex flex-column align-items-center justify-content-center" style="max-width:800px; width:100%; margin:auto;">
                                                                                <div id="pdfjs-viewer-<?= $row['id'] ?>" style="width:100%; aspect-ratio: 16/9; background:#222; border-radius:16px; overflow:hidden; box-shadow:0 2px 16px #0002; border:2px solid #444; min-height:60vh; max-height:70vh;"></div>
                                                                            </div>
                                                                            <div class="mt-3 text-center">
                                                                                <small class="text-light">Puedes navegar, buscar, hacer zoom y descargar el PDF desde el visor avanzado.</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer justify-content-between bg-dark rounded-bottom-4 flex-wrap gap-2 animate__animated animate__fadeInUp animate__faster">
                                                                            <a href="../<?= htmlspecialchars($row['archivo_pdf']) ?>" target="_blank" class="btn btn-outline-primary d-flex align-items-center gap-2 fw-semibold shadow-sm" style="min-width:180px;">
                                                                                <i class="mdi mdi-open-in-new"></i> <span>Abrir en nueva pestaña</span>
                                                                            </a>
                                                                            <a href="../<?= htmlspecialchars($row['archivo_pdf']) ?>" download class="btn btn-success d-flex align-items-center gap-2 fw-semibold shadow-sm" style="min-width:150px;">
                                                                                <i class="mdi mdi-download"></i> <span>Descargar PDF</span>
                                                                            </a>
                                                                            <button type="button" class="btn btn-danger px-4 d-flex align-items-center gap-2 fw-semibold shadow-sm" data-bs-dismiss="modal">
                                                                                <i class="mdi mdi-close"></i> <span>Cerrar</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted">No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $row['disponible'] ? 'Sí' : 'No' ?></td>
                                                    <td>
                                                        <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                                        <a href="delete_book.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este libro?')">Eliminar</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
function renderPDF(pdfUrl, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '<div class="text-light text-center p-4">Cargando PDF...</div>';
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
        container.innerHTML = '';
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            pdf.getPage(pageNum).then(function(page) {
                const scale = 1.2;
                const viewport = page.getViewport({ scale: scale });
                const canvas = document.createElement('canvas');
                canvas.className = 'mb-3 shadow-sm rounded';
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                container.appendChild(canvas);
                page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport });
            });
        }
    });
}
// Integración con Bootstrap modal para cargar PDF.js solo cuando se abre
<?php foreach($libros as $row): if ($row['archivo_pdf']): ?>
$('#pdfModal<?= $row['id'] ?>').on('shown.bs.modal', function () {
    renderPDF('../<?= htmlspecialchars($row['archivo_pdf']) ?>', 'pdfjs-viewer-<?= $row['id'] ?>');
});
$('#pdfModal<?= $row['id'] ?>').on('hidden.bs.modal', function () {
    document.getElementById('pdfjs-viewer-<?= $row['id'] ?>').innerHTML = '';
});
<?php endif; endforeach; ?>
</script>
</body>
</html>
