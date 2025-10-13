<?php
$servername = "localhost";
$username = "root";
$password = "Info2025/*-";
$dbname = "biblioteca";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para listar los libros
function listarLibros($conn) {
    $sql = "SELECT * FROM libros";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Función para agregar un nuevo libro
function agregarLibro($conn, $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $archivo_pdf, $tipo_libro) {
    $sql = "INSERT INTO libros (titulo, autor, genero, año_publicacion, isbn, descripcion, archivo_pdf, tipo_libro)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Vincular los parámetros
    $stmt->bind_param("sssissss", $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $archivo_pdf, $tipo_libro);

    // Ejecutar la consulta
    return $stmt->execute();
}

// Agregar libro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $genero = trim($_POST['genero']);
    $año_publicacion = trim($_POST['año_publicacion']);
    $isbn = trim($_POST['isbn']);
    $descripcion = trim($_POST['descripcion']);
    $archivo_pdf_tmp = $_FILES['archivo_pdf']['tmp_name'];
    $archivo_pdf_name = $_FILES['archivo_pdf']['name'];
    $tipo_libro = trim($_POST['tipo_libro']);

    // Validar que se haya seleccionado un archivo PDF para libros digitales
    if ($tipo_libro === 'digital' && empty($archivo_pdf_tmp)) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Debe seleccionar un archivo PDF para libros digitales.'
                });
              </script>";
    } else {
        // Mover el archivo PDF a una ubicación segura (opcional, pero recomendado)
        $upload_dir = 'uploads/'; // Crea esta carpeta en tu servidor
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $archivo_pdf_path = '';
        if (!empty($archivo_pdf_tmp)) {
            $archivo_pdf_path = $upload_dir . uniqid() . '_' . $archivo_pdf_name;
            move_uploaded_file($archivo_pdf_tmp, $archivo_pdf_path);
        }

        if (agregarLibro($conn, $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $archivo_pdf_path, $tipo_libro)) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Libro agregado exitosamente'
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al agregar el libro'
                    });
                  </script>";
        }
    }
}

// Listar libros
$libros = listarLibros($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .container.mt-5 {
            max-width: 1400px; /* Aumenta el ancho máximo del contenedor principal */
            margin-left: auto;
            margin-right: auto;
            padding-left: 15px;
            padding-right: 15px;
        }

        .main-title h3 {
            margin-bottom: 2rem;
            text-align: center;
            color: #007bff;
        }

        /* Estilos para centrar el formulario de agregar libro */
        .agregar-libro-container {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .agregar-libro-card {
            width: 90%; /* Aumenta el ancho del formulario */
            max-width: 800px; /* Aumenta el ancho máximo del formulario */
            border: 1px solid #ced4da;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .agregar-libro-card .card-header {
            background-color: #f8f9fa;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #ced4da;
        }

        .agregar-libro-card .card-header h5 {
            margin-bottom: 0;
            color: #343a40;
            font-weight: bold;
            text-align: center;
        }

        .agregar-libro-card .card-body {
            padding: 1.25rem;
        }

        .form-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #007bff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            margin-top: 1rem;
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .table-responsive {
            margin-top: 2rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 0;
            color: #212529;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
            text-align: left;
        }

        .table thead th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
            font-weight: bold;
            color: #343a40;
            text-align: center;
        }

        .table tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table a {
            color: #007bff;
            text-decoration: none;
        }

        .table a:hover {
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
        }

        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body class="crm_body_bg">
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="header_iner d-flex justify-content-between align-items-center">
                        <div class="serach_field-area">
                            <div class="search_inner">
                                <form action="#">
                                    <div class="search_field">
                                        <input type="text" placeholder="Buscar libros...">
                                    </div>
                                    <button type="submit">
                                        <img src="./img/icon/icon_search.svg" alt>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <div class="main-title">
                    <h3 class="mb-0">Administrar Biblioteca - Libros</h3>
                </div>

                <div class="agregar-libro-container">
                    <div class="agregar-libro-card">
                        <div class="card-header">
                            <h5>Agregar Nuevo Libro</h5>
                        </div>
                        <div class="card-body">
                            <form action="libros.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label" for="titulo">Título:</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="autor">Autor:</label>
                                    <input type="text" class="form-control" id="autor" name="autor" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="genero">Género:</label>
                                    <input type="text" class="form-control" id="genero" name="genero">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="año_publicacion">Año de Publicación:</label>
                                    <input type="number" class="form-control" id="año_publicacion" name="año_publicacion">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="isbn">ISBN:</label>
                                    <input type="text" class="form-control" id="isbn" name="isbn">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="descripcion">Descripción:</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="tipo_libro">Tipo de Libro:</label>
                                    <select class="form-control" id="tipo_libro" name="tipo_libro" required>
                                        <option value="digital">Digital</option>
                                        <option value="fisico">Físico</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="archivo_pdf">Archivo PDF:</label>
                                    <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept=".pdf">
                                    <small class="form-text text-muted">Solo para libros digitales.</small>
                                </div>
                                <button type="submit" name="agregar" class="btn btn-primary">Agregar Libro</button>
                            </form>
                        </div>
                    </div>
                </div>

                <h2 class="mt-4 text-center">Libros Disponibles</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center">Título</th>
                                <th class="text-center">Autor</th>
                                <th class="text-center">Género</th>
                                <th class="text-center">ISBN</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Archivo PDF</th>
                                <th class="text-center">Tipo de Libro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($libros)): ?>
                                <?php foreach ($libros as $libro): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($libro['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($libro['autor']); ?></td>
                                        <td><?php echo htmlspecialchars($libro['genero']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($libro['isbn']); ?></td>
                                        <td><?php echo htmlspecialchars($libro['descripcion']); ?></td>
                                        <td class="text-center">
                                            <?php if ($libro['tipo_libro'] === 'digital' && !empty($libro['archivo_pdf'])): ?>
                                                <a href="<?php echo htmlspecialchars($libro['archivo_pdf']); ?>" target="_blank">Ver PDF</a>
                                            <?php elseif ($libro['tipo_libro'] === 'fisico'): ?>
                                                Libro Físico
                                            <?php else: ?>
                                                No disponible
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo htmlspecialchars($libro['tipo_libro']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay libros disponibles</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./vendors/count_up/jquery.waypoints.min.js"></script>
    <script src="./vendors/chartlist/Chart.min.js"></script>
    <script src="./vendors/count_up/jquery.counterup.min.js"></script>
    <script src="./vendors/swiper_slider/js/swiper.min.js"></script>
    <script src="./vendors/niceselect/js/jquery.nice-select.min.js"></script>
    <script src="./vendors/owl_carousel/js/owl.carousel.min.js"></script>
    <script src="./vendors/gijgo/gijgo.min.js"></script>
    <script src="./vendors/datatable/js/jquery.dataTables.min.js"></script>
    <script src="./vendors/datatable/js/dataTables.responsive.min.js"></script>
    <script src="./vendors/datatable/js/dataTables.buttons.min.js"></script>
    <script src="./vendors/datatable/js/jszip.min.js"></script>
    <script src="./vendors/datatable/js/pdfmake.min.js"></script>
    <script src="./vendors/datatable/js/vfs_fonts.js"></script>
    <script src="./vendors/datatable/js/buttons.print.min.js"></script>
    <script src="./js/chart.min.js"></script>
    <script src="./js/custom.js"></script>
</body>

</html>