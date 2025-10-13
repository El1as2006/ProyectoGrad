<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

// Endpoint AJAX para búsqueda de estudiante por carnet -
if ((isset($_GET['carnet']) || isset($_GET['carnet_manual'])) && isset($_GET['ajax'])) {
    $carnet = isset($_GET['carnet_manual']) ? trim($_GET['carnet_manual']) : trim($_GET['carnet']);
    header('Content-Type: application/json');

    if (preg_match('/^\d{8}$/', $carnet)) {
        $stmt = $conn->prepare("SELECT id, nombre FROM estudiantes WHERE carnet_e = ? AND activo = 1");
        if ($stmt) {
            $stmt->bind_param('s', $carnet);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo json_encode(['success' => true, 'id' => $row['id'], 'nombre' => $row['nombre']]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se encontró estudiante activo con ese carnet.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Error SQL: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Carnet inválido. Debe contener 8 dígitos.']);
    }
    $conn->close();
    exit;
}

// Obtener estudiantes y libros para los selects
$estudiantes = [];
$stmtEst = $conn->prepare("SELECT id, nombre, carnet_e AS carnet FROM estudiantes WHERE activo = 1 ORDER BY nombre");
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
    $id_estudiante = $_POST['estudiante_id'] ?? '';
    $id_libro = $_POST['libro_id'] ?? '';
    $fecha_prestamo = $_POST['fecha_prestamo'] ?? '';
    $fecha_devolucion = $_POST['fecha_devolucion'] ?? '';
    $status = 'no entregado';

    if (!$id_estudiante) { $errores[] = 'Selecciona un estudiante.'; }
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
    $stmtCheck = $conn->prepare('SELECT COUNT(*) FROM prestamos WHERE id_estudiante = ? AND id_libro = ? AND status = "no entregado"');
    $stmtCheck->bind_param('ii', $id_estudiante, $id_libro);
    $stmtCheck->execute();
    $stmtCheck->bind_result($prestamo_activo);
    $stmtCheck->fetch();
    $stmtCheck->close();
    if ($prestamo_activo > 0) {
        $errores[] = 'Este estudiante ya tiene un préstamo activo de este libro.';
    }

    if (empty($errores)) {
        // Obtener el carnet del estudiante
        $stmtCarnet = $conn->prepare('SELECT carnet_e FROM estudiantes WHERE id = ?');
        $stmtCarnet->bind_param('i', $id_estudiante);
        $stmtCarnet->execute();
        $stmtCarnet->bind_result($carnet_estudiante);
        $stmtCarnet->fetch();
        $stmtCarnet->close();
        
        // Insertar préstamo con campos adicionales para estudiantes
        $stmt = $conn->prepare('INSERT INTO prestamos (id_usuario, id_libro, fecha_prestamo, fecha_devolucion, status, tipo_usuario, carnet_e, origen_usuario, id_estudiante) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $tipo_usuario = 'estudiante';
        $origen_usuario = 'estudiante';
        // id_usuario se mantiene igual a id_estudiante para compatibilidad
        $stmt->bind_param('iissssssi', $id_estudiante, $id_libro, $fecha_prestamo, $fecha_devolucion, $status, $tipo_usuario, $carnet_estudiante, $origen_usuario, $id_estudiante);
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
            
            // Notificación para todos los admins
            $tipo = 'prestamo';
            $admins = $conn->query("SELECT id_usuario FROM usuarios WHERE rol='admin' OR rol='super_admin'");
            while($admin = $admins->fetch_assoc()) {
                $stmtNotifA = $conn->prepare('INSERT INTO notificaciones (usuario_id, tipo, mensaje) VALUES (?, ?, ?)');
                $msg = 'Nuevo préstamo realizado por el estudiante (ID: ' . $id_estudiante . ', Carnet: ' . $carnet_estudiante . ')';
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
        $stmt = $conn->prepare("SELECT id, nombre FROM estudiantes WHERE carnet_e = ? AND activo = 1");
        if ($stmt) {
            $stmt->bind_param('s', $carnet);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                // Redirigir a add_loan.php con el id del estudiante
                header("Location: add_loan.php?estudiante_id=" . $row['id']);
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
                                                    <option value="<?= $est['id'] ?>" <?= $estudiante_id == $est['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($est['nombre']) ?> 
                                                        <?php if (!empty($est['carnet'])): ?>
                                                            - Carnet: <?= htmlspecialchars($est['carnet']) ?>
                                                        <?php endif; ?>
                                                    </option>
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
                                            <form action="add_loan.php" method="get" class="row g-2 align-items-end" id="form-buscar-carnet">
                                                <div class="col-md-5">
                                                    <label for="carnetInput" class="form-label">Carnet del Estudiante</label>
                                                    <input type="text" id="carnetInput" name="carnet_manual" class="form-control" maxlength="8" pattern="\d{8}" placeholder="Ej: 12345678" required autocomplete="off">
                                                    <small class="form-text text-muted">8 dígitos</small>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="btn btn-success w-100"><i class="mdi mdi-magnify"></i> Buscar</button>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="button" class="btn btn-primary w-100" onclick="escanearQR()"><i class="mdi mdi-qrcode-scan"></i> Escanear QR</button>
                                                </div>
                                            </form>
                                            <div id="qr-scanner" style="display:none; margin-top:15px;">
                                                <div id="qr-reader" style="width: 100%;"></div>
                                                <div class="mt-2 text-center">
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="cerrarEscaner()"><i class="mdi mdi-close"></i> Cerrar Escáner</button>
                                                </div>
                                            </div>
                                            <div id="mensaje-resultado" style="margin-top:10px;"></div>
                                        </div>
                                    </div>
                                    <script src="../assets/js/html5-qrcode.min.js"></script>
                                    <script>
                                    let html5QrCodeInstance = null;
                                    let isScanning = false;

                                    function escanearQR() {
                                        const qrDiv = document.getElementById('qr-scanner');
                                        const qrReader = document.getElementById('qr-reader');
                                        const mensajeDiv = document.getElementById('mensaje-resultado');
                                        
                                        // Si ya está escaneando, detener primero
                                        if (isScanning) {
                                            cerrarEscaner();
                                            return;
                                        }
                                        
                                        qrDiv.style.display = 'block';
                                        qrReader.innerHTML = '';
                                        mensajeDiv.innerHTML = '<div class="alert alert-info"><i class="mdi mdi-loading mdi-spin"></i> Preparando cámara...</div>';
                                        
                                        iniciarEscaneo();
                                        
                                        function iniciarEscaneo() {
                                            // Limpiar instancia previa si existe
                                            if (html5QrCodeInstance) {
                                                try {
                                                    html5QrCodeInstance.clear();
                                                } catch (e) {
                                                    console.log('Limpieza de instancia previa:', e);
                                                }
                                            }
                                            
                                            html5QrCodeInstance = new Html5Qrcode("qr-reader");
                                            
                                            Html5Qrcode.getCameras().then(cameras => {
                                                if (cameras && cameras.length > 0) {
                                                    console.log('Cámaras disponibles:', cameras.length);
                                                    
                                                    // Preferir cámara trasera si está disponible
                                                    let cameraId = cameras[cameras.length - 1].id;
                                                    
                                                    mensajeDiv.innerHTML = '<div class="alert alert-success"><i class="mdi mdi-camera"></i> Cámara activada. Coloque el código QR del carnet frente a la cámara.</div>';
                                                    isScanning = true;
                                                    
                                                    const config = { 
                                                        fps: 10,
                                                        qrbox: { width: 250, height: 250 },
                                                        aspectRatio: 1.0,
                                                        disableFlip: false
                                                    };
                                                    
                                                    html5QrCodeInstance.start(
                                                        cameraId,
                                                        config,
                                                        (decodedText, decodedResult) => {
                                                            // QR detectado exitosamente
                                                            console.log('QR detectado:', decodedText);
                                                            const carnet = decodedText.trim();
                                                            
                                                            // Detener el escáner inmediatamente
                                                            cerrarEscaner();
                                                            
                                                            document.getElementById('carnetInput').value = carnet;
                                                            mensajeDiv.innerHTML = '<div class="alert alert-success"><strong><i class="mdi mdi-check-circle"></i> QR Escaneado:</strong> ' + carnet + '</div>';
                                                            
                                                            // Validar y buscar
                                                            if (/^\d{8}$/.test(carnet)) {
                                                                buscarEstudiante(carnet);
                                                            } else {
                                                                mensajeDiv.innerHTML = '<div class="alert alert-warning"><i class="mdi mdi-alert"></i> El código escaneado no tiene el formato correcto de carnet (debe ser 8 dígitos).<br>Valor escaneado: <strong>' + carnet + '</strong></div>';
                                                            }
                                                        },
                                                        (errorMessage) => {
                                                            // Error al escanear (normal, ocurre constantemente)
                                                            // No mostrar estos errores
                                                        }
                                                    ).catch(err => {
                                                        console.error('Error al iniciar cámara:', err);
                                                        mensajeDiv.innerHTML = '<div class="alert alert-danger"><strong><i class="mdi mdi-alert-circle"></i> Error:</strong> No se pudo acceder a la cámara. ' + err + '<br><small>Asegúrate de permitir el acceso a la cámara en tu navegador.</small></div>';
                                                        isScanning = false;
                                                        qrDiv.style.display = 'none';
                                                    });
                                                } else {
                                                    mensajeDiv.innerHTML = '<div class="alert alert-danger"><i class="mdi mdi-camera-off"></i> No se detectó ninguna cámara disponible en este dispositivo.</div>';
                                                    qrDiv.style.display = 'none';
                                                    isScanning = false;
                                                }
                                            }).catch(err => {
                                                console.error('Error al buscar cámaras:', err);
                                                mensajeDiv.innerHTML = '<div class="alert alert-danger"><strong><i class="mdi mdi-alert-circle"></i> Error:</strong> No se pudo buscar cámaras. ' + err + '</div>';
                                                qrDiv.style.display = 'none';
                                                isScanning = false;
                                            });
                                        }
                                    }

                                    function cerrarEscaner() {
                                        console.log('Cerrando escáner...');
                                        const qrDiv = document.getElementById('qr-scanner');
                                        const qrReader = document.getElementById('qr-reader');
                                        
                                        if (html5QrCodeInstance && isScanning) {
                                            html5QrCodeInstance.stop().then(() => {
                                                console.log('Escáner detenido correctamente');
                                                html5QrCodeInstance.clear();
                                                qrReader.innerHTML = '';
                                                qrDiv.style.display = 'none';
                                                isScanning = false;
                                                html5QrCodeInstance = null;
                                            }).catch((err) => {
                                                console.error('Error al detener escáner:', err);
                                                qrReader.innerHTML = '';
                                                qrDiv.style.display = 'none';
                                                isScanning = false;
                                                html5QrCodeInstance = null;
                                            });
                                        } else {
                                            qrReader.innerHTML = '';
                                            qrDiv.style.display = 'none';
                                            isScanning = false;
                                            html5QrCodeInstance = null;
                                        }
                                    }

                                    function buscarEstudiante(carnet) {
                                        const mensajeDiv = document.getElementById('mensaje-resultado');
                                        mensajeDiv.innerHTML = '<div class="alert alert-info"><i class="mdi mdi-loading mdi-spin"></i> Buscando estudiante con carnet <strong>' + carnet + '</strong>...</div>';
                                        
                                        console.log('Buscando estudiante con carnet:', carnet);
                                        
                                        fetch('add_loan.php?carnet_manual=' + encodeURIComponent(carnet) + '&ajax=1')
                                            .then(response => {
                                                console.log('Response status:', response.status);
                                                if (!response.ok) {
                                                    throw new Error('Error HTTP: ' + response.status);
                                                }
                                                return response.text();
                                            })
                                            .then(text => {
                                                console.log('Respuesta del servidor:', text);
                                                
                                                // Intentar parsear como JSON
                                                try {
                                                    const data = JSON.parse(text);
                                                    console.log('Datos parseados:', data);
                                                    
                                                    if (data.success) {
                                                        mensajeDiv.innerHTML = '<div class="alert alert-success"><strong><i class="mdi mdi-check-circle"></i> Estudiante encontrado:</strong> ' + data.nombre + '<br><small>Redirigiendo...</small></div>';
                                                        setTimeout(() => {
                                                            window.location.href = 'add_loan.php?estudiante_id=' + data.id;
                                                        }, 1000);
                                                    } else {
                                                        mensajeDiv.innerHTML = '<div class="alert alert-danger"><strong><i class="mdi mdi-close-circle"></i> Error:</strong> ' + data.error + '</div>';
                                                        setTimeout(() => {
                                                            mensajeDiv.innerHTML = '';
                                                        }, 4000);
                                                    }
                                                } catch (e) {
                                                    console.error('Error al parsear JSON:', e);
                                                    console.error('Respuesta recibida:', text);
                                                    mensajeDiv.innerHTML = '<div class="alert alert-danger"><strong><i class="mdi mdi-alert-circle"></i> Error:</strong> La respuesta del servidor no es válida.<br><small>Revisa la consola del navegador (F12) para más detalles.</small></div>';
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error de conexión:', error);
                                                mensajeDiv.innerHTML = '<div class="alert alert-danger"><strong><i class="mdi mdi-alert-circle"></i> Error de conexión:</strong> No se pudo conectar con el servidor.<br><small>' + error.message + '</small></div>';
                                            });
                                    }

                                    // Formulario de búsqueda manual
                                    document.getElementById('form-buscar-carnet').addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        var carnet = document.getElementById('carnetInput').value.trim();
                                        
                                        if (!/^\d{8}$/.test(carnet)) {
                                            const mensajeDiv = document.getElementById('mensaje-resultado');
                                            mensajeDiv.innerHTML = '<div class="alert alert-danger"><i class="mdi mdi-alert"></i> El carnet debe tener exactamente 8 dígitos.</div>';
                                            document.getElementById('carnetInput').classList.add('is-invalid');
                                            setTimeout(() => {
                                                document.getElementById('carnetInput').classList.remove('is-invalid');
                                                mensajeDiv.innerHTML = '';
                                            }, 2000);
                                            return;
                                        }
                                        
                                        buscarEstudiante(carnet);
                                    });

                                    // Auto-focus en el campo de carnet
                                    document.getElementById('carnetInput').focus();
                                    
                                    // Limpiar escáner al salir de la página
                                    window.addEventListener('beforeunload', function() {
                                        if (isScanning) {
                                            cerrarEscaner();
                                        }
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
