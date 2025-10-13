<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$mensaje = '';
$error = '';
$qr_generado = false;
$estudiante = null;

// Generar QR para un estudiante específico
if (isset($_POST['generar_qr'])) {
    $estudiante_id = $_POST['estudiante_id'] ?? '';
    
    if ($estudiante_id) {
        $stmt = $conn->prepare("SELECT id, nombre, carnet_e FROM estudiantes WHERE id = ? AND activo = 1");
        $stmt->bind_param('i', $estudiante_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $estudiante = $row;
            
            // Generar QR con el carnet del estudiante
            require_once '../assets/phpqrcode/qrcode.php';
            $qr_dir = '../uploads/qr/estudiantes/';
            if (!is_dir($qr_dir)) {
                mkdir($qr_dir, 0777, true);
            }
            
            $qr_data = $row['carnet_e'];
            $qr_file = $qr_dir . "estudiante_{$estudiante_id}.png";
            
            $qr = QRCode::getMinimumQRCode($qr_data, QR_ERROR_CORRECT_LEVEL_L);
            $img = $qr->createImage(8, 4);
            imagepng($img, $qr_file);
            imagedestroy($img);
            
            $qr_generado = true;
            $mensaje = 'Código QR generado exitosamente.';
        } else {
            $error = 'No se encontró el estudiante.';
        }
        $stmt->close();
    } else {
        $error = 'Debes seleccionar un estudiante.';
    }
}

// Obtener lista de estudiantes
$estudiantes = [];
$stmtEst = $conn->prepare("SELECT id, nombre, carnet_e FROM estudiantes WHERE activo = 1 ORDER BY nombre");
if ($stmtEst) {
    $stmtEst->execute();
    $resultEst = $stmtEst->get_result();
    while ($row = $resultEst->fetch_assoc()) {
        $estudiantes[] = $row;
    }
    $stmtEst->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Generar QR Estudiantes | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        .qr-container {
            text-align: center;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .qr-container img {
            max-width: 300px;
            margin: 15px auto;
        }
        @media print {
            .no-print {
                display: none;
            }
            .qr-container {
                border: 2px solid #000;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/session_check.php'; ?>
        <?php include 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Generar Código QR de Estudiantes</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="mdi mdi-qrcode"></i> Generar QR</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="mdi mdi-check-circle me-2"></i><?= $mensaje ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="mdi mdi-alert-circle me-2"></i><?= $error ?>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form method="post">
                                        <div class="mb-3">
                                            <label for="estudiante_id" class="form-label">Seleccionar Estudiante</label>
                                            <select class="form-select" id="estudiante_id" name="estudiante_id" required>
                                                <option value="">-- Selecciona un estudiante --</option>
                                                <?php foreach($estudiantes as $est): ?>
                                                    <option value="<?= $est['id'] ?>" <?= ($estudiante && $estudiante['id'] == $est['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($est['nombre']) ?> - Carnet: <?= htmlspecialchars($est['carnet_e'] ?? 'Sin carnet') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="form-text text-muted">
                                                Este código QR contendrá el número de carnet del estudiante para facilitar los préstamos.
                                            </small>
                                        </div>
                                        <button type="submit" name="generar_qr" class="btn btn-primary">
                                            <i class="mdi mdi-qrcode-scan"></i> Generar Código QR
                                        </button>
                                    </form>

                                    <div class="mt-4">
                                        <h6 class="text-muted"><i class="mdi mdi-information"></i> Información:</h6>
                                        <ul class="small text-muted">
                                            <li>El código QR contiene el número de carnet del estudiante (8 dígitos)</li>
                                            <li>Puede ser escaneado en la página de registro de préstamos</li>
                                            <li>El estudiante puede imprimir o guardar su código QR para uso futuro</li>
                                            <li>Facilita el proceso de préstamo de libros</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($qr_generado && $estudiante): ?>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="mdi mdi-check-circle"></i> QR Generado</h5>
                                </div>
                                <div class="card-body">
                                    <div class="qr-container">
                                        <h5><?= htmlspecialchars($estudiante['nombre']) ?></h5>
                                        <p class="text-muted mb-2">Carnet: <strong><?= htmlspecialchars($estudiante['carnet_e']) ?></strong></p>
                                        <img src="../uploads/qr/estudiantes/estudiante_<?= $estudiante['id'] ?>.png" alt="QR del Estudiante" class="img-fluid">
                                        <p class="small text-muted mt-2">Escanea este código QR para registrar préstamos</p>
                                    </div>
                                    
                                    <div class="text-center mt-3 no-print">
                                        <button type="button" class="btn btn-info" onclick="window.print()">
                                            <i class="mdi mdi-printer"></i> Imprimir
                                        </button>
                                        <a href="../uploads/qr/estudiantes/estudiante_<?= $estudiante['id'] ?>.png" download="QR_<?= htmlspecialchars($estudiante['nombre']) ?>.png" class="btn btn-secondary">
                                            <i class="mdi mdi-download"></i> Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="mdi mdi-account-multiple"></i> Lista de Estudiantes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Carnet</th>
                                                    <th>QR Generado</th>
                                                    <th class="no-print">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($estudiantes as $est): 
                                                    $qr_path = "../uploads/qr/estudiantes/estudiante_{$est['id']}.png";
                                                    $qr_existe = file_exists($qr_path);
                                                ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($est['nombre']) ?></td>
                                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($est['carnet_e'] ?? 'Sin carnet') ?></span></td>
                                                    <td>
                                                        <?php if ($qr_existe): ?>
                                                            <span class="badge bg-success"><i class="mdi mdi-check"></i> Sí</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning"><i class="mdi mdi-close"></i> No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="no-print">
                                                        <form method="post" style="display: inline;">
                                                            <input type="hidden" name="estudiante_id" value="<?= $est['id'] ?>">
                                                            <button type="submit" name="generar_qr" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-qrcode"></i> Generar
                                                            </button>
                                                        </form>
                                                        <?php if ($qr_existe): ?>
                                                        <a href="<?= $qr_path ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="mdi mdi-eye"></i> Ver
                                                        </a>
                                                        <?php endif; ?>
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
</body>
</html>
