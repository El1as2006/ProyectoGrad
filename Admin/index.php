<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../conexion.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$nombre = $_SESSION['user_name'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'admin';

function safe_count_query($conn, $sql) {
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_row()) {
        return $row[0];
    } else {
        error_log("SQL Error: $sql | " . $conn->error);
        return 0;
    }
}

$total_estudiantes = safe_count_query($conn, "SELECT COUNT(*) FROM usuarios WHERE rol = 'estudiante'");
$total_libros = safe_count_query($conn, "SELECT COUNT(*) FROM libros");
$total_prestamos = safe_count_query($conn, "SELECT COUNT(*) FROM prestamos");

$top_libros = $conn->query("SELECT l.titulo, COUNT(*) as total FROM prestamos p JOIN libros l ON p.id_libro = l.id GROUP BY l.titulo ORDER BY total DESC LIMIT 5");
$libros_mas_prestados = $top_libros ? $top_libros->fetch_all(MYSQLI_ASSOC) : [];
// Préstamos recientes
$prestamos_recientes = $conn->query("SELECT u.nombre, l.titulo, p.fecha_prestamo FROM prestamos p JOIN usuarios u ON p.id_usuario = u.id_usuario JOIN libros l ON p.id_libro = l.id ORDER BY p.fecha_prestamo DESC LIMIT 5");
$ultimos_prestamos = $prestamos_recientes ? $prestamos_recientes->fetch_all(MYSQLI_ASSOC) : [];

// Préstamos por mes (últimos 12 meses)
$prestamos_mes = $conn->query("SELECT DATE_FORMAT(fecha_prestamo, '%Y-%m') as mes, COUNT(*) as total FROM prestamos GROUP BY mes ORDER BY mes DESC LIMIT 12");
$prestamos_por_mes = array_reverse($prestamos_mes ? $prestamos_mes->fetch_all(MYSQLI_ASSOC) : []);

// Libros por categoría
$libros_categoria = $conn->query("SELECT categoria, COUNT(*) as total FROM libros GROUP BY categoria");
$libros_por_categoria = $libros_categoria ? $libros_categoria->fetch_all(MYSQLI_ASSOC) : [];

// Top usuarios con más préstamos
$top_usuarios = $conn->query("SELECT u.nombre, COUNT(*) as total FROM prestamos p JOIN usuarios u ON p.id_usuario = u.id_usuario GROUP BY u.nombre ORDER BY total DESC LIMIT 5");
$usuarios_mas_prestamos = $top_usuarios ? $top_usuarios->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Dashboard | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/session_check.php'; ?>
        <?php include 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row mt-4">
                        <div class="col-12">
                            <h2 class="mb-4">Bienvenido, <?= htmlspecialchars($nombre) ?></h2>
                        </div>
                        <div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-center shadow-lg border-0 animate__animated animate__fadeInUp">
            <div class="card-body">
                <i class="mdi mdi-account-multiple text-primary" style="font-size: 2.5rem;"></i>
                <h5 class="card-title mt-2">Estudiantes</h5>
                <h3 class="fw-bold"><?= $total_estudiantes ?></h3>
                <p class="card-text">Registrados</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-lg border-0 animate__animated animate__fadeInUp animate__delay-1s">
            <div class="card-body">
                <i class="mdi mdi-book-open-page-variant text-warning" style="font-size: 2.5rem;"></i>
                <h5 class="card-title mt-2">Libros</h5>
                <h3 class="fw-bold"><?= $total_libros ?></h3>
                <p class="card-text">Registrados</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-lg border-0 animate__animated animate__fadeInUp animate__delay-2s">
            <div class="card-body">
                <i class="mdi mdi-bookmark-check text-success" style="font-size: 2.5rem;"></i>
                <h5 class="card-title mt-2">Préstamos</h5>
                <h3 class="fw-bold"><?= $total_prestamos ?></h3>
                <p class="card-text">Realizados</p>
            </div>
        </div>
    </div>
</div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Libros más prestados</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <?php if (empty($libros_mas_prestados)): ?>
                                            <li class="list-group-item">No hay datos suficientes.</li>
                                        <?php else: ?>
                                            <?php foreach ($libros_mas_prestados as $libro): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?= htmlspecialchars($libro['titulo']) ?>
                                                    <span class="badge bg-info"><?= $libro['total'] ?> préstamos</span>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Préstamos recientes</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <?php if (empty($ultimos_prestamos)): ?>
                                            <li class="list-group-item">No hay préstamos recientes.</li>
                                        <?php else: ?>
                                            <?php foreach ($ultimos_prestamos as $prestamo): ?>
                                                <li class="list-group-item">
                                                    <strong><?= htmlspecialchars($prestamo['nombre']) ?></strong> prestó <em><?= htmlspecialchars($prestamo['titulo']) ?></em> el <?= date('d/m/Y', strtotime($prestamo['fecha_prestamo'])) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Préstamos por mes (último año)</h5>
                                </div>
                                <div class="card-body">
                                    <div id="chart-prestamos-mes" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">Libros por categoría</h5>
                                </div>
                                <div class="card-body">
                                    <div id="chart-libros-categoria" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Top usuarios con más préstamos</h5>
                                </div>
                                <div class="card-body">
                                    <div id="chart-usuarios-prestamos" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
    <script src="includes/notifications.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Datos desde PHP
        const prestamosPorMes = <?php echo json_encode($prestamos_por_mes); ?>;
        const librosPorCategoria = <?php echo json_encode($libros_por_categoria); ?>;
        const usuariosMasPrestamos = <?php echo json_encode($usuarios_mas_prestamos); ?>;

        // Gráfica de préstamos por mes
        if(document.querySelector("#chart-prestamos-mes")) {
            const chartPrestamosMes = new ApexCharts(document.querySelector("#chart-prestamos-mes"), {
                chart: { type: 'line', height: 300 },
                series: [{
                    name: 'Préstamos',
                    data: prestamosPorMes.map(x => parseInt(x.total))
                }],
                xaxis: {
                    categories: prestamosPorMes.map(x => x.mes),
                    title: { text: 'Mes' }
                },
                yaxis: { title: { text: 'Cantidad' } },
                colors: ['#007bff']
            });
            chartPrestamosMes.render();
        }
        // Gráfica de libros por categoría
        if(document.querySelector("#chart-libros-categoria")) {
            const chartLibrosCategoria = new ApexCharts(document.querySelector("#chart-libros-categoria"), {
                chart: { type: 'pie', height: 300 },
                series: librosPorCategoria.map(x => parseInt(x.total)),
                labels: librosPorCategoria.map(x => x.categoria),
                colors: ['#ffc107', '#28a745', '#17a2b8', '#6c757d', '#007bff', '#dc3545']
            });
            chartLibrosCategoria.render();
        }
        // Gráfica de top usuarios con más préstamos
        if(document.querySelector("#chart-usuarios-prestamos")) {
            const chartUsuariosPrestamos = new ApexCharts(document.querySelector("#chart-usuarios-prestamos"), {
                chart: { type: 'bar', height: 300 },
                series: [{
                    name: 'Préstamos',
                    data: usuariosMasPrestamos.map(x => parseInt(x.total))
                }],
                xaxis: {
                    categories: usuariosMasPrestamos.map(x => x.nombre),
                    title: { text: 'Usuario' }
                },
                yaxis: { title: { text: 'Cantidad' } },
                colors: ['#6c757d']
            });
            chartUsuariosPrestamos.render();
        }
    </script>
</body>
</html>
