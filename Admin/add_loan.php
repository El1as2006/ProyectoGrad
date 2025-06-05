<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

// Obtener estudiantes y libros para los selects
$estudiantes = [];
$stmtEst = $conn->prepare("SELECT id_usuario AS id, nombre FROM usuarios WHERE rol = 'estudiante' ORDER BY nombre");
if ($stmtEst) {
    $stmtEst->execute();
    $resultEst = $stmtEst->get_result();
    while ($row = $resultEst->fetch_assoc()) {
        $estudiantes[] = $row;
    }
    $stmtEst->close();
} else {
    $errores[] = 'Error al obtener estudiantes: ' . $conn->error;
}
$libros = $conn->query('SELECT id, titulo FROM libros WHERE disponible = 1 ORDER BY titulo');

$mensaje = '';
$errores = [];
$estudiante_id = $_POST['estudiante_id'] ?? ($_GET['estudiante_id'] ?? '');
$libro_id = $_POST['libro_id'] ?? '';
$fecha_prestamo = $_POST['fecha_prestamo'] ?? '';
$fecha_devolucion = $_POST['fecha_devolucion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_POST['estudiante_id'] ?? '';
    $id_libro = $_POST['libro_id'] ?? '';
    $fecha_prestamo = $_POST['fecha_prestamo'] ?? '';
    $fecha_devolucion = $_POST['fecha_devolucion'] ?? '';
    $status = 'no entregado';

    if (!$id_usuario) { $errores[] = 'Selecciona un estudiante.'; }
    if (!$id_libro) { $errores[] = 'Selecciona un libro.'; }
    if (!$fecha_prestamo) { $errores[] = 'La fecha de préstamo es obligatoria.'; }
    if (!$fecha_devolucion) { $errores[] = 'La fecha de devolución es obligatoria.'; }
    if ($fecha_prestamo && $fecha_devolucion && $fecha_prestamo > $fecha_devolucion) { $errores[] = 'La fecha de devolución debe ser posterior a la de préstamo.'; }

    // Verificar que el libro esté disponible
    $libro_disp = $conn->prepare('SELECT disponible FROM libros WHERE id = ?');
    $libro_disp->bind_param('i', $id_libro);
    $libro_disp->execute();
    $libro_disp->bind_result($disponible);
    $libro_disp->fetch();
    $libro_disp->close();
    if ($disponible != 1) {
        $errores[] = 'El libro seleccionado no está disponible.';
    }

    // Verificar que el estudiante no tenga un préstamo activo del mismo libro
    $stmtCheck = $conn->prepare('SELECT COUNT(*) FROM prestamos WHERE id_usuario = ? AND id_libro = ? AND status = "no entregado"');
    $stmtCheck->bind_param('ii', $id_usuario, $id_libro);
    $stmtCheck->execute();
    $stmtCheck->bind_result($prestamo_activo);
    $stmtCheck->fetch();
    $stmtCheck->close();
    if ($prestamo_activo > 0) {
        $errores[] = 'Este estudiante ya tiene un préstamo activo de este libro.';
    }

    if (empty($errores)) {
        $stmt = $conn->prepare('INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('iisss', $id_usuario, $id_libro, $fecha_prestamo, $fecha_devolucion, $status);
        if ($stmt->execute()) {
            $prestamo_id = $stmt->insert_id;
            // Generar QR para el préstamo
            require_once '../assets/phpqrcode/qrcode.php';
            $qr_dir = '../uploads/qr/';
            if (!is_dir($qr_dir)) { mkdir($qr_dir, 0777, true); }
            $qr_data = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/detalle_prestamo.php?id=" . $prestamo_id . "&accion=devolver";
            $qr_file = $qr_dir . "prestamo_{$prestamo_id}.png";
            $qr = QRCode::getMinimumQRCode($qr_data, QR_ERROR_CORRECT_LEVEL_L);
            $img = $qr->createImage(6, 2);
            imagepng($img, $qr_file);
            imagedestroy($img);
            // Guardar ruta del QR en la base de datos
            $stmtQr = $conn->prepare('UPDATE prestamos SET qr_prestamo = ? WHERE id_prestamo = ?');
            $qr_db_path = 'uploads/qr/prestamo_' . $prestamo_id . '.png';
            $stmtQr->bind_param('si', $qr_db_path, $prestamo_id);
            $stmtQr->execute();
            $stmtQr->close();
            // Marcar libro como no disponible
            $conn->query("UPDATE libros SET disponible = 0 WHERE id = $id_libro");
            // Notificación para el estudiante
            $tipo = 'prestamo';
            $mensaje_notif = 'Nuevo préstamo registrado. Fecha devolución: ' . $fecha_devolucion;
            $stmtNotif = $conn->prepare('INSERT INTO notificaciones (usuario_id, tipo, mensaje) VALUES (?, ?, ?)');
            $stmtNotif->bind_param('iss', $id_usuario, $tipo, $mensaje_notif);
            $stmtNotif->execute();
            $stmtNotif->close();
            // Notificación para todos los admins
            $admins = $conn->query("SELECT id_usuario FROM usuarios WHERE rol='admin' OR rol='super_admin'");
            while($admin = $admins->fetch_assoc()) {
                $stmtNotifA = $conn->prepare('INSERT INTO notificaciones (usuario_id, tipo, mensaje) VALUES (?, ?, ?)');
                $msg = 'Nuevo préstamo realizado por el estudiante ID: ' . $id_usuario;
                $stmtNotifA->bind_param('iss', $admin['id_usuario'], $tipo, $msg);
                $stmtNotifA->execute();
                $stmtNotifA->close();
            }
            $mensaje = 'Préstamo registrado correctamente. <br> <b>QR generado:</b><br><img src="../' . $qr_db_path . '" alt="QR Préstamo" style="width:120px;">';
            $estudiante_id = $libro_id = $fecha_prestamo = $fecha_devolucion = '';
        } else {
            $errores[] = 'Error al registrar el préstamo.';
        }
        $stmt->close();
    }
}

// Si se recibe carnet por GET, buscar estudiante y redirigir a registro de préstamo
if (isset($_GET['carnet']) || isset($_GET['carnet_manual'])) {
    $carnet = isset($_GET['carnet_manual']) ? trim($_GET['carnet_manual']) : trim($_GET['carnet']);
    if (preg_match('/^\d{8}$/', $carnet)) {
        $stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE carnet = ? AND rol = 'estudiante'");
        if ($stmt) {
            $stmt->bind_param('s', $carnet);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                // Redirigir a add_loan.php con el id del estudiante
                header("Location: add_loan.php?estudiante_id=" . $row['id_usuario']);
                exit;
            } else {
                $error = "No se encontró estudiante con ese carnet.";
            }
            $stmt->close();
        } else {
            $error = "Error SQL: " . $conn->error;
        }
    } else {
        $error = "Carnet inválido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Registrar Préstamo | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/session_check.php'; ?>
        <?php include 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">Registrar Préstamo</h4>
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
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                    <?php endif; ?>
                                    <form method="post" novalidate>
                                        <div class="mb-3">
                                            <label for="estudiante_id" class="form-label">Estudiante</label>
                                            <select class="form-select" id="estudiante_id" name="estudiante_id" required>
                                                <option value="">Selecciona un estudiante</option>
                                                <?php foreach($estudiantes as $est): ?>
                                                    <option value="<?= $est['id'] ?>" <?= $estudiante_id == $est['id'] ? 'selected' : '' ?>><?= htmlspecialchars($est['nombre']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="libro_id" class="form-label">Libro</label>
                                            <select class="form-select" id="libro_id" name="libro_id" required>
                                                <option value="">Selecciona un libro</option>
                                                <?php while($lib = $libros->fetch_assoc()): ?>
                                                    <option value="<?= $lib['id'] ?>" <?= $libro_id == $lib['id'] ? 'selected' : '' ?>><?= htmlspecialchars($lib['titulo']) ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fecha_prestamo" class="form-label">Fecha de Préstamo</label>
                                            <input type="date" class="form-control" id="fecha_prestamo" name="fecha_prestamo" value="<?= htmlspecialchars($fecha_prestamo) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fecha_devolucion" class="form-label">Fecha de Devolución</label>
                                            <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" value="<?= htmlspecialchars($fecha_devolucion) ?>" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Registrar</button>
                                    </form>
                                    <!-- Búsqueda rápida de estudiante por carnet -->
                                    <div class="card mt-4 mb-0">
                                        <div class="card-header bg-info text-white">
                                            <b>Búsqueda rápida de estudiante por carnet</b>
                                        </div>
                                        <div class="card-body">
                                            <form action="add_loan.php" method="get" class="row g-2 align-items-center" id="form-buscar-carnet">
                                                <div class="col-auto">
                                                    <label for="carnetInput" class="form-label mb-0">Carnet</label>
                                                    <input type="text" id="carnetInput" name="carnet_manual" class="form-control" maxlength="8" pattern="\\d{8}" placeholder="Escanea o ingresa carnet" required autocomplete="off">
                                                </div>
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-success">Buscar</button>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-secondary" onclick="escanearQR()">Escanear QR</button>
                                                </div>
                                            </form>
                                            <div id="qr-scanner" style="display:none; margin-top:10px;"></div>
                                        </div>
                                    </div>
                                    <script src="../assets/js/html5-qrcode.min.js"></script>
                                    <script>
                                    let html5QrCodeInstance = null;
                                    function escanearQR() {
                                        const qrDiv = document.getElementById('qr-scanner');
                                        qrDiv.style.display = 'block';
                                        qrDiv.innerHTML = '';
                                        if (html5QrCodeInstance) {
                                            html5QrCodeInstance.stop().then(() => {
                                                html5QrCodeInstance.clear();
                                                iniciarEscaneo();
                                            }).catch(() => {
                                                iniciarEscaneo();
                                            });
                                        } else {
                                            iniciarEscaneo();
                                        }
                                        function iniciarEscaneo() {
                                            html5QrCodeInstance = new Html5Qrcode("qr-scanner");
                                            Html5Qrcode.getCameras().then(cameras => {
                                                if (cameras && cameras.length) {
                                                    html5QrCodeInstance.start(
                                                        { facingMode: "environment" },
                                                        { fps: 10, qrbox: 200 },
                                                        qrCodeMessage => {
                                                            document.getElementById('carnetInput').value = qrCodeMessage;
                                                            html5QrCodeInstance.stop().then(() => {
                                                                html5QrCodeInstance.clear();
                                                                qrDiv.innerHTML = '';
                                                                qrDiv.style.display = 'none';
                                                            });
                                                        },
                                                        errorMessage => {}
                                                    ).catch(err => {
                                                        qrDiv.innerHTML = '<span class="text-danger">No se pudo acceder a la cámara: ' + err + '</span>';
                                                    });
                                                } else {
                                                    qrDiv.innerHTML = '<span class="text-danger">No se detectó ninguna cámara disponible.</span>';
                                                }
                                            }).catch(err => {
                                                qrDiv.innerHTML = '<span class="text-danger">Error al buscar cámaras: ' + err + '</span>';
                                            });
                                        }
                                    }
                                    document.getElementById('form-buscar-carnet').addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        var carnet = document.getElementById('carnetInput').value.trim();
                                        if (!/^\d{8}$/.test(carnet)) {
                                            document.getElementById('carnetInput').classList.add('is-invalid');
                                            setTimeout(() => document.getElementById('carnetInput').classList.remove('is-invalid'), 1500);
                                            return;
                                        }
                                        fetch('add_loan.php?carnet_manual=' + carnet + '&ajax=1')
                                            .then(r => r.json())
                                            .then(data => {
                                                if (data.success) {
                                                    window.location.href = 'add_loan.php?estudiante_id=' + data.id;
                                                } else {
                                                    let qrDiv = document.getElementById('qr-scanner');
                                                    qrDiv.innerHTML = '<div class="alert alert-danger mt-2">' + data.error + '</div>';
                                                    qrDiv.style.display = 'block';
                                                    setTimeout(() => { qrDiv.innerHTML = ''; qrDiv.style.display = 'none'; }, 2500);
                                                }
                                            });
                                    });
                                    </script>
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
<?php
// Endpoint AJAX para búsqueda de estudiante por carnet
if ((isset($_GET['carnet']) || isset($_GET['carnet_manual'])) && isset($_GET['ajax'])) {
    $carnet = isset($_GET['carnet_manual']) ? trim($_GET['carnet_manual']) : trim($_GET['carnet']);
    header('Content-Type: application/json');
    if (preg_match('/^\d{8}$/', $carnet)) {
        $stmt = $conn->prepare("SELECT id_usuario, nombre FROM usuarios WHERE carnet = ? AND rol = 'estudiante'");
        if ($stmt) {
            $stmt->bind_param('s', $carnet);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo json_encode(['success' => true, 'id' => $row['id_usuario']]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se encontró estudiante con ese carnet.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Error SQL: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Carnet inválido.']);
    }
    exit;
}
?>
