<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "Info2025/*-";
$dbname = "biblioteca";

$conn = new mysqli($servername, $username, $password, $dbname);

// Establecer la codificación a UTF-8
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$alertMessage = "";
$alertType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['estudiantes']) && is_array($_POST['estudiantes'])) {
        $estudiantes = $_POST['estudiantes'];
        $errors = [];

        foreach ($estudiantes as $index => $estudiante) {
            $nombre = trim($estudiante['nombre']);
            $gmail_institucional = trim($estudiante['gmail_institucional']);
            $contraseña = trim($estudiante['contraseña']);
            $repetir_contraseña = trim($estudiante['repetir_contraseña']);
            $rol = 'estudiante';

            // Validar que el nombre solo contenga letras y espacios
            if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
                $errors[] = "El nombre del estudiante " . ($index + 1) . " solo puede contener letras y espacios.";
                continue;
            }

            if (empty($nombre) || empty($gmail_institucional) || empty($contraseña) || empty($repetir_contraseña)) {
                $errors[] = "Todos los campos del estudiante " . ($index + 1) . " son obligatorios y no deben contener solo espacios en blanco.";
                continue;
            }

            // Validar formato de correo electrónico
            if (!filter_var($gmail_institucional, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "El correo electrónico del estudiante " . ($index + 1) . " no es válido.";
                continue;
            }

            // Verificar si el correo electrónico ya existe en la base de datos
            $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE gmail_institucional = ?");
            if ($stmt_check === false) {
                $errors[] = "Error en la preparación de la consulta de verificación para el estudiante " . ($index + 1) . ": " . $conn->error;
                continue;
            }
            $stmt_check->bind_param("s", $gmail_institucional);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $errors[] = "El correo electrónico " . htmlspecialchars($gmail_institucional) . " ya está registrado para otro usuario.";
                $stmt_check->close();
                continue;
            }
            $stmt_check->close();

            // Validar que las contraseñas coincidan
            if ($contraseña !== $repetir_contraseña) {
                $errors[] = "Las contraseñas para el estudiante " . ($index + 1) . " no coinciden.";
                continue;
            }

            $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, gmail_institucional, contraseña, rol) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                $errors[] = "Error en la preparación de la consulta de inserción para el estudiante " . ($index + 1) . ": " . $conn->error;
                continue;
            }

            $stmt->bind_param("ssss", $nombre, $gmail_institucional, $contraseña_encriptada, $rol);

            if (!$stmt->execute()) {
                $errors[] = "Error al añadir el estudiante " . ($index + 1) . ": " . $stmt->error;
            }

            $stmt->close();
        }

        $conn->close();

        if (empty($errors)) {
            $alertMessage = "Estudiantes registrados exitosamente";
            $alertType = "success";
        } else {
            $alertMessage = implode("<br>", $errors);
            $alertType = "error";
        }
    } else {
        $alertMessage = "No se enviaron los datos de los estudiantes correctamente.";
        $alertType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include 'header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .container.mt-5 {
            max-width: 90%;
            margin: 30px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .main-title h3 {
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .card-header {
            background-color: #e9ecef;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h5 {
            margin-bottom: 0;
            font-size: 1.1rem;
        }

        .card-body {
            padding: 15px;
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 0.5rem;
            display: block;
            color: #495057;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
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

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            color: #fff;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            color: #fff;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: #fff;
        }

        #estudiantesContainer {
            margin-bottom: 20px;
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
                                        <input type="text" placeholder="Buscar aquí...">
                                    </div>
                                    <button type="submit">
                                        <img src="./img/icon/icon_search.svg" alt="Buscar">
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <div class="main-title">
                    <h3 class="mb-0">Registro de Estudiantes</h3>
                </div>

                <form action="añadir_estudiante.php" method="POST" id="formEstudiantes">
                    <div id="estudiantesContainer">
                        </div>
                    <button type="button" class="btn btn-secondary mt-3" onclick="agregarFormulario()">Añadir Estudiante</button>
                    <button type="submit" class="btn btn-primary mt-3">Registrar Estudiantes</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        let contadorEstudiantes = 0;

        function agregarFormulario() {
            contadorEstudiantes++;
            const container = document.getElementById('estudiantesContainer');

            const estudianteDiv = document.createElement('div');
            estudianteDiv.classList.add('card', 'mb-3');
            estudianteDiv.setAttribute('id', `estudiante${contadorEstudiantes}`);
            estudianteDiv.innerHTML = `
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Estudiante ${contadorEstudiantes}</h5>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFormulario(${contadorEstudiantes})">Eliminar</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="nombre${contadorEstudiantes}">Nombre:</label>
                        <input type="text" class="form-control" id="nombre${contadorEstudiantes}" name="estudiantes[${contadorEstudiantes - 1}][nombre]" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="gmail_institucional${contadorEstudiantes}">Correo Institucional:</label>
                        <input type="email" class="form-control" id="gmail_institucional${contadorEstudiantes}" name="estudiantes[${contadorEstudiantes - 1}][gmail_institucional]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="contraseña${contadorEstudiantes}">Contraseña:</label>
                        <input type="password" class="form-control" id="contraseña${contadorEstudiantes}" name="estudiantes[${contadorEstudiantes - 1}][contraseña]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="repetir_contraseña${contadorEstudiantes}">Repetir Contraseña:</label>
                        <input type="password" class="form-control" id="repetir_contraseña${contadorEstudiantes}" name="estudiantes[${contadorEstudiantes - 1}][repetir_contraseña]" required>
                    </div>
                </div>
            `;
            container.appendChild(estudianteDiv);

            // Añadir validación en tiempo real para el campo de nombre
            document.getElementById(`nombre${contadorEstudiantes}`).addEventListener('input', function(event) {
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            });
        }

        function eliminarFormulario(id) {
            const estudianteDiv = document.getElementById(`estudiante${id}`);
            estudianteDiv.remove();
        }

        <?php if ($alertMessage): ?>
            Swal.fire({
                icon: '<?php echo $alertType; ?>',
                title: '<?php echo $alertType == "success" ? "Éxito" : "Error"; ?>',
                html: '<?php echo $alertMessage; ?>'
            }).then(function() {
                if ('<?php echo $alertType; ?>' === 'success') {
                    window.location = 'añadir_estudiante.php';
                }
            });
        <?php endif; ?>

        // Agregar un formulario inicial al cargar la página
        agregarFormulario();
    </script>

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