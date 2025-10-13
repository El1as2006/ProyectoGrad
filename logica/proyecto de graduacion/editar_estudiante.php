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

$id_estudiante = $_GET['id'] ?? '';

if (!is_numeric($id_estudiante)) {
    echo "ID no válido o no numérico.";
    exit();
}

$stmt_select = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ? AND rol = 'estudiante'");
$stmt_select->bind_param("i", $id_estudiante);
$stmt_select->execute();
$result_select = $stmt_select->get_result();
$estudiante = $result_select->fetch_assoc();
$stmt_select->close();

if (!$estudiante) {
    echo "Estudiante no encontrado.";
    exit();
}

$alertMessage = "";
$alertType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $gmail_institucional = trim($_POST['gmail_institucional']);
    $contraseña = trim($_POST['contraseña'] ?? '');
    $repetir_contraseña = trim($_POST['repetir_contraseña'] ?? '');

    if (empty($nombre) || empty($gmail_institucional)) {
        $alertMessage = "Los campos Nombre y Correo Institucional son obligatorios y no deben contener solo espacios en blanco.";
        $alertType = "error";
    } else {
        // Verificar si el nuevo correo ya existe para otro usuario
        if ($gmail_institucional !== $estudiante['gmail_institucional']) {
            $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE gmail_institucional = ?");
            $stmt_check->bind_param("s", $gmail_institucional);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $alertMessage = "El correo electrónico " . htmlspecialchars($gmail_institucional) . " ya está registrado para otro usuario.";
                $alertType = "error";
                $stmt_check->close();
            } else {
                $stmt_check->close();
                $stmt_update = null;
                if (!empty($contraseña)) {
                    if ($contraseña !== $repetir_contraseña) {
                        $alertMessage = "Las contraseñas no coinciden.";
                        $alertType = "error";
                    } else {
                        $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);
                        $stmt_update = $conn->prepare("UPDATE usuarios SET nombre = ?, gmail_institucional = ?, contraseña = ? WHERE id_usuario = ?");
                        $stmt_update->bind_param("sssi", $nombre, $gmail_institucional, $contraseña_encriptada, $id_estudiante);
                    }
                } else {
                    $stmt_update = $conn->prepare("UPDATE usuarios SET nombre = ?, gmail_institucional = ? WHERE id_usuario = ?");
                    $stmt_update->bind_param("ssi", $nombre, $gmail_institucional, $id_estudiante);
                }

                if ($stmt_update) {
                    if ($stmt_update->execute()) {
                        if ($stmt_update->affected_rows > 0) {
                            $alertMessage = "Estudiante actualizado exitosamente";
                            $alertType = "success";
                        } else {
                            $alertMessage = "No se realizaron cambios.";
                            $alertType = "info";
                        }
                    } else {
                        $alertMessage = "Error al actualizar el estudiante.";
                        $alertType = "error";
                    }
                    $stmt_update->close();
                }
            }
        } else {
            // El correo no ha cambiado, proceder a la actualización sin verificar duplicados
            $stmt_update = null;
            if (!empty($contraseña)) {
                if ($contraseña !== $repetir_contraseña) {
                    $alertMessage = "Las contraseñas no coinciden.";
                    $alertType = "error";
                } else {
                    $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);
                    $stmt_update = $conn->prepare("UPDATE usuarios SET nombre = ?, gmail_institucional = ?, contraseña = ? WHERE id_usuario = ?");
                    $stmt_update->bind_param("sssi", $nombre, $gmail_institucional, $contraseña_encriptada, $id_estudiante);
                }
            } else {
                $stmt_update = $conn->prepare("UPDATE usuarios SET nombre = ?, gmail_institucional = ? WHERE id_usuario = ?");
                $stmt_update->bind_param("ssi", $nombre, $gmail_institucional, $id_estudiante);
            }

            if ($stmt_update) {
                if ($stmt_update->execute()) {
                    if ($stmt_update->affected_rows > 0) {
                        $alertMessage = "Estudiante actualizado exitosamente";
                        $alertType = "success";
                    } else {
                        $alertMessage = "No se realizaron cambios.";
                        $alertType = "info";
                    }
                } else {
                    $alertMessage = "Error al actualizar el estudiante.";
                    $alertType = "error";
                }
                $stmt_update->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estudiante</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <div class="main-title text-center">
                    <h3 class="mb-4">Editar Estudiante</h3>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <form action="editar_estudiante.php?id=<?php echo $id_estudiante; ?>" method="POST">
                                    <div class="form-group">
                                        <label for="nombre">Nombre:</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($estudiante['nombre']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="gmail_institucional">Correo Institucional:</label>
                                        <input type="email" class="form-control" id="gmail_institucional" name="gmail_institucional" value="<?php echo htmlspecialchars($estudiante['gmail_institucional']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contraseña">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                                        <input type="password" class="form-control" id="contraseña" name="contraseña">
                                    </div>
                                    <div class="form-group">
                                        <label for="repetir_contraseña">Repetir Nueva Contraseña:</label>
                                        <input type="password" class="form-control" id="repetir_contraseña" name="repetir_contraseña">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Actualizar Estudiante</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($alertMessage): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $alertType; ?>',
                title: '<?php echo $alertType == "success" ? "Éxito" : ($alertType == "info" ? "Información" : "Error"); ?>',
                text: '<?php echo $alertMessage; ?>'
            }).then(function() {
                if ('<?php echo $alertType; ?>' === 'success') {
                    window.location = 'historial_estudiantes.php';
                }
            });
        </script>
    <?php endif; ?>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./js/custom.js"></script>
</body>
</html>