<?php
session_start();

$host = 'localhost';
$dbname = 'biblioteca';
$username = 'root';
$password = 'Info2025/*-';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$alertMessage = "";
$alertType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_usuario']) && isset($_POST['fecha_devolucion']) && isset($_POST['libros']) && is_array($_POST['libros']) && !empty($_POST['libros'])) {
        $id_usuario = $_POST['id_usuario'];
        $fecha_prestamo = date('Y-m-d');
        $fecha_devolucion = $_POST['fecha_devolucion'];
        $libros = $_POST['libros'];
        $status = 'no entregado';

        try {
            $pdo->beginTransaction();
            $prestamoExitoso = true;
            foreach ($libros as $id_libro) {
                // Insertar el préstamo
                $sql = "INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, status) VALUES (:id_usuario, :id_libro, :fecha_prestamo, :fecha_devolucion, :status)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
                $stmt->bindParam(':id_libro', $id_libro, PDO::PARAM_INT);
                $stmt->bindParam(':fecha_prestamo', $fecha_prestamo, PDO::PARAM_STR);
                $stmt->bindParam(':fecha_devolucion', $fecha_devolucion, PDO::PARAM_STR);
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
                $stmt->execute();

                // Actualizar el inventario del libro
                $sqlUpdate = "UPDATE libros SET disponible = disponible - 1 WHERE id = :id_libro AND disponible > 0";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':id_libro', $id_libro, PDO::PARAM_INT);
                $stmtUpdate->execute();

                if ($stmtUpdate->rowCount() == 0) {
                    $prestamoExitoso = false;
                    $alertMessage .= "No hay suficientes copias disponibles del libro con ID: " . htmlspecialchars($id_libro) . ". ";
                }
            }

            if ($prestamoExitoso) {
                $pdo->commit();
                $alertMessage = "Préstamo realizado correctamente.";
                $alertType = "success";
            } else {
                $pdo->rollBack();
                $alertType = "warning";
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            $alertMessage = "Error en la consulta: " . $e->getMessage();
            $alertType = "danger";
        }
    } else {
        $alertMessage = "Por favor, selecciona un estudiante y al menos un libro.";
        $alertType = "warning";
    }
}

// Obtener lista de libros físicos disponibles
$sqlLibros = "SELECT id, titulo, autor, genero FROM libros WHERE tipo_libro = 'fisico' AND disponible > 0";
$stmtLibros = $pdo->query($sqlLibros);
$libros = $stmtLibros->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de estudiantes
$sqlEstudiantes = "SELECT id_usuario, nombre, gmail_institucional FROM usuarios WHERE rol = 'estudiante'";
$stmtEstudiantes = $pdo->query($sqlEstudiantes);
$estudiantes = $stmtEstudiantes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préstamo de Libros</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .main-title h3 {
            margin-bottom: 2rem;
            text-align: center;
            color: #007bff;
        }
        .libro-seleccionado { 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .libro-seleccionado button {
            margin-left: 10px;
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
                                        <input type="text" placeholder="Search here...">
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
                    <h3 class="mb-0">Préstamo de Libros</h3>
                </div>

                <?php if ($alertMessage): ?>
                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $alertMessage; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <h4>Libros Físicos Disponibles</h4>
                        <ul class="list-group">
                            <?php if (empty($libros)): ?>
                                <li class="list-group-item">No hay libros físicos disponibles.</li>
                            <?php else: ?>
                                <?php foreach ($libros as $libro): ?>
                                    <li class="list-group-item">
                                        <strong><?php echo htmlspecialchars($libro['titulo']); ?></strong><br>
                                        Autor: <?php echo htmlspecialchars($libro['autor']); ?><br>
                                        Género: <?php echo htmlspecialchars($libro['genero']); ?><br>
                                        <button class="btn btn-primary mt-2" onclick="agregarLibro(<?php echo $libro['id']; ?>, '<?php echo htmlspecialchars($libro['titulo']); ?>')">Seleccionar</button>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Realizar Préstamo</h4>
                        <form action="prestamo_libros.php" method="POST">
                            <div class="form-group">
                                <label for="librosSeleccionados">Libros Seleccionados</label>
                                <ul class="list-group" id="librosSeleccionados"></ul>
                            </div>
                            <div class="form-group">
                                <label for="buscar_estudiante">Buscar Estudiante</label>
                                <input type="text" class="form-control" id="buscar_estudiante" placeholder="Buscar estudiante...">
                                <div id="resultados_estudiantes" class="list-group mt-2"></div>
                            </div>
                            <div class="form-group">
                                <label for="id_usuario">Carnet del Estudiante</label>
                                <select class="form-control" id="id_usuario" name="id_usuario" required>
                                    <option value="">Seleccione un estudiante</option>
                                    <?php foreach ($estudiantes as $estudiante): ?>
                                        <option value="<?php echo $estudiante['id_usuario']; ?>"><?php echo htmlspecialchars($estudiante['nombre']) . ' (' . htmlspecialchars($estudiante['gmail_institucional']) . ')'; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fecha_devolucion">Fecha de Devolución</label>
                                <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Realizar Préstamo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        let librosSeleccionadosArray = [];

        function agregarLibro(id, titulo) {
            const librosSeleccionados = document.getElementById('librosSeleccionados');
            if (!librosSeleccionadosArray.includes(id)) {
                librosSeleccionadosArray.push(id);
                const libroItem = document.createElement('li');
                libroItem.classList.add('list-group-item', 'libro-seleccionado');
                libroItem.setAttribute('id', `libro-${id}`);
                libroItem.innerHTML = `
                    <span>${titulo}</span>
                    <input type="hidden" name="libros[]" value="${id}">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarLibro(${id})">Eliminar</button>
                `;
                librosSeleccionados.appendChild(libroItem);
                console.log("Libro agregado:", { id, titulo, librosSeleccionadosArray });
            } else {
                console.log("El libro ya ha sido seleccionado:", { id, titulo });
            }
        }

        function eliminarLibro(id) {
            const libroItem = document.getElementById(`libro-${id}`);
            if (libroItem) {
                libroItem.remove();
                librosSeleccionadosArray = librosSeleccionadosArray.filter(libroId => libroId !== id);
                console.log("Libro eliminado:", { id, librosSeleccionadosArray });
            }
        }

        $(document).ready(function() {
            $('#buscar_estudiante').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                console.log("Buscando estudiante:", value);
                if (value.length > 2) {
                    $.ajax({
                        url: 'buscar_estudiantes.php',
                        method: 'GET',
                        data: { query: value },
                        success: function(response) {
                            console.log("Respuesta de búsqueda de estudiantes:", response);
                            $('#resultados_estudiantes').html(response);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error en la búsqueda de estudiantes:", error);
                            $('#resultados_estudiantes').html('<li class="list-group-item">Error al buscar estudiantes.</li>');
                        }
                    });
                } else {
                    $('#resultados_estudiantes').html('');
                }
            });

            $(document).on('click', '#resultados_estudiantes .list-group-item', function() {
                var id = $(this).data('id');
                var nombre = $(this).text();
                console.log("Estudiante seleccionado:", { id, nombre });
                $('#id_usuario').val(id);
                $('#id_usuario option:selected').text(nombre);
                $('#resultados_estudiantes').html('');
                $('#buscar_estudiante').val('');
            });

            $('form').on('submit', function(event) {
                const selectedUserId = $('#id_usuario').val();
                if (!selectedUserId) {
                    alert('Por favor, selecciona un estudiante.');
                    event.preventDefault(); // Evita que el formulario se envíe
                    return;
                }
                if (librosSeleccionadosArray.length === 0) {
                    alert('Por favor, selecciona al menos un libro.');
                    event.preventDefault(); // Evita que el formulario se envíe
                    return;
                }
                console.log("Formulario enviado con:", { id_usuario: selectedUserId, libros: librosSeleccionadosArray, fecha_devolucion: $('#fecha_devolucion').val() });
            });
        });
    </script>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./js/custom.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>