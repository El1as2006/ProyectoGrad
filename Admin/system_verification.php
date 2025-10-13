<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$verificaciones = [];

// 1. Verificar conexión a base de datos
try {
    $verificaciones['db'] = $conn ? 'Conexión a base de datos: OK' : 'Error de conexión a BD';
} catch (Exception $e) {
    $verificaciones['db'] = 'Error de conexión: ' . $e->getMessage();
}

// 2. Verificar tabla categorias_libros
$result = $conn->query("SHOW TABLES LIKE 'categorias_libros'");
$verificaciones['tabla_categorias'] = $result && $result->num_rows > 0 
    ? 'Tabla categorias_libros: OK' 
    : 'Tabla categorias_libros: NO EXISTE';

// 3. Verificar categorías en la base de datos
$result = $conn->query("SELECT COUNT(*) as total FROM categorias_libros");
$total_categorias = $result ? $result->fetch_assoc()['total'] : 0;
$verificaciones['categorias_count'] = "Categorías en BD: $total_categorias";

// 4. Verificar columna categoria_id en tabla libros
$result = $conn->query("SHOW COLUMNS FROM libros LIKE 'categoria_id'");
$verificaciones['columna_categoria'] = $result && $result->num_rows > 0 
    ? 'Columna categoria_id en libros: OK' 
    : 'Columna categoria_id: NO EXISTE';

// 5. Verificar archivos CSS
$archivos_css = [
    '../assets/css/vendor.min.css',
    '../assets/css/app-saas.min.css',
    '../assets/css/icons.min.css',
    '../assets/css/material-icons-fix.css'
];

foreach ($archivos_css as $archivo) {
    $verificaciones['css_' . basename($archivo)] = file_exists($archivo) 
        ? "CSS " . basename($archivo) . ": OK" 
        : "CSS " . basename($archivo) . ": NO EXISTE";
}

// 6. Verificar archivos de fuentes Material Design
$archivos_fuentes = [
    '../assets/fonts/materialdesignicons-webfont1d2d.woff2',
    '../assets/fonts/materialdesignicons-webfont1d2d.woff',
    '../assets/fonts/materialdesignicons-webfont1d2d.ttf'
];

foreach ($archivos_fuentes as $archivo) {
    $verificaciones['font_' . pathinfo($archivo, PATHINFO_EXTENSION)] = file_exists($archivo) 
        ? "Fuente " . pathinfo($archivo, PATHINFO_EXTENSION) . ": OK" 
        : "Fuente " . pathinfo($archivo, PATHINFO_EXTENSION) . ": NO EXISTE";
}

// 7. Verificar archivo AJAX de notificaciones
$verificaciones['ajax_notifications'] = file_exists('includes/ajax_notifications.php') 
    ? 'AJAX notifications: OK' 
    : 'AJAX notifications: NO EXISTE';

// 8. Verificar archivo JavaScript de notificaciones
$verificaciones['js_notifications'] = file_exists('includes/notifications.js') 
    ? 'JavaScript notifications: OK' 
    : 'JavaScript notifications: NO EXISTE';

// 9. Probar notificaciones (simulación)
try {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_notif = $result->fetch_assoc()['total'];
    $verificaciones['notificaciones'] = "Notificaciones en BD: $total_notif";
    $stmt->close();
} catch (Exception $e) {
    $verificaciones['notificaciones'] = 'Error al consultar notificaciones: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Verificación del Sistema | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/material-icons-fix.css" rel="stylesheet" type="text/css" />
    <style>
        .verification-item {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }
        .verification-ok {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .verification-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .icon-test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .icon-test-item {
            text-align: center;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        .icon-test-item i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
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
                                <h4 class="page-title">Verificación del Sistema</h4>
                                <p class="text-muted">Estado de las funcionalidades implementadas</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Estado de Componentes</h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($verificaciones as $clave => $mensaje): ?>
                                        <div class="verification-item <?= strpos($mensaje, 'OK') !== false || strpos($mensaje, ':') !== false ? 'verification-ok' : 'verification-error' ?>">
                                            <i class="mdi mdi-<?= strpos($mensaje, 'OK') !== false || strpos($mensaje, ':') !== false ? 'check-circle' : 'alert-circle' ?> me-2"></i>
                                            <?= htmlspecialchars($mensaje) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Prueba de Iconos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="icon-test-grid">
                                        <div class="icon-test-item">
                                            <i class="mdi mdi-book"></i>
                                            <small>mdi-book</small>
                                        </div>
                                        <div class="icon-test-item">
                                            <i class="mdi mdi-flask"></i>
                                            <small>mdi-flask</small>
                                        </div>
                                        <div class="icon-test-item">
                                            <i class="mdi mdi-palette"></i>
                                            <small>mdi-palette</small>
                                        </div>
                                        <div class="icon-test-item">
                                            <i class="mdi mdi-school"></i>
                                            <small>mdi-school</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Acciones Rápidas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="categorias.php" class="btn btn-primary">
                                            <i class="mdi mdi-folder"></i> Gestionar Categorías
                                        </a>
                                        <a href="list_books.php" class="btn btn-info">
                                            <i class="mdi mdi-book"></i> Ver Libros
                                        </a>
                                        <a href="test_notifications.php" class="btn btn-warning">
                                            <i class="mdi mdi-bell"></i> Probar Notificaciones
                                        </a>
                                        <button onclick="testAjaxNotifications()" class="btn btn-success">
                                            <i class="mdi mdi-wifi"></i> Test AJAX
                                        </button>
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
    
    <script>
        function testAjaxNotifications() {
            fetch('includes/ajax_notifications.php')
                .then(response => response.json())
                .then(data => {
                    alert('AJAX Test: ' + JSON.stringify(data, null, 2));
                })
                .catch(error => {
                    alert('AJAX Error: ' + error.message);
                });
        }
    </script>
</body>
</html>
