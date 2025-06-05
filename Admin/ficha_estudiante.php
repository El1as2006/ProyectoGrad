<?php
// ficha_estudiante.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include '../conexion.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$estudiante = null;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT * FROM usuarios WHERE id_usuario = ? AND rol = "estudiante"');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $estudiante = $res->fetch_assoc();
    $stmt->close();
}
$qr_path = $estudiante ? '../uploads/qr/carnet_' . $estudiante['carnet'] . '.png' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Ficha del Estudiante</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-xl-7">
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">Ficha del Estudiante</h4>
                                </div>
                                <div class="card-body">
                                    <?php if ($estudiante): ?>
                                        <h3><?= htmlspecialchars($estudiante['nombre']) ?></h3>
                                        <p><b>Carnet:</b> <?= htmlspecialchars($estudiante['carnet']) ?></p>
                                        <p><b>Email:</b> <?= htmlspecialchars($estudiante['gmail_institucional']) ?></p>
                                        <p><b>Teléfono:</b> <?= htmlspecialchars($estudiante['telefono']) ?></p>
                                        <p><b>QR del carnet:</b><br>
                                            <?php if (file_exists($qr_path)): ?>
                                                <img src="<?= $qr_path ?>" alt="QR Carnet" style="width:180px;">
                                            <?php else: ?>
                                                <span class="text-danger">QR no disponible</span>
                                            <?php endif; ?>
                                        </p>
                                        <hr>
                                        <h5>Buscar y registrar préstamo por carnet</h5>
                                        <form action="add_loan.php" method="get" class="row g-2 align-items-center">
                                            <input type="hidden" name="carnet" value="<?= htmlspecialchars($estudiante['carnet']) ?>">
                                            <div class="col-auto">
                                                <label for="carnetInput" class="form-label mb-0">Carnet</label>
                                                <input type="text" id="carnetInput" name="carnet_manual" class="form-control" value="<?= htmlspecialchars($estudiante['carnet']) ?>" maxlength="8" pattern="\d{8}" required>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-success">Registrar préstamo</button>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-secondary" onclick="escanearQR()">Escanear QR</button>
                                            </div>
                                        </form>
                                        <div id="qr-scanner" style="display:none; margin-top:15px;"></div>
                                        <script src="../assets/js/html5-qrcode.min.js"></script>
                                        <script>
                                        function escanearQR() {
                                            document.getElementById('qr-scanner').style.display = 'block';
                                            if (!window.qrScannerLoaded) {
                                                const qrDiv = document.getElementById('qr-scanner');
                                                const html5QrCode = new Html5Qrcode("qr-scanner");
                                                html5QrCode.start(
                                                    { facingMode: "environment" },
                                                    { fps: 10, qrbox: 200 },
                                                    qrCodeMessage => {
                                                        document.getElementById('carnetInput').value = qrCodeMessage;
                                                        html5QrCode.stop();
                                                        qrDiv.innerHTML = '';
                                                        qrDiv.style.display = 'none';
                                                    },
                                                    errorMessage => {}
                                                ).catch(err => {
                                                    qrDiv.innerHTML = '<span class="text-danger">No se pudo acceder a la cámara</span>';
                                                });
                                                window.qrScannerLoaded = true;
                                            }
                                        }
                                        </script>
                                    <?php else: ?>
                                        <div class="alert alert-danger">Estudiante no encontrado.</div>
                                    <?php endif; ?>
                                    <a href="list_students.php" class="btn btn-primary mt-3">Volver a la lista</a>
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
