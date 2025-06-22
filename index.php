<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión y guardar en $conn
$conn = include_once 'conexion.php';

// Redirigir si el usuario no ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: Admin/login.php");
    exit;
}

// Datos de sesión
$nombre = $_SESSION['user_name'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'estudiante';
$id_usuario = $_SESSION['user_id'];

// Función para contar resultados de una consulta
function safe_count_query($conn, $sql) {
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_row()) {
        return $row[0];
    } else {
        error_log("SQL Error: $sql | " . $conn->error);
        return 0;
    }
}

// Consultas de estadísticas
$total_prestados = safe_count_query($conn, "
    SELECT COUNT(*) FROM prestamos 
    WHERE id_usuario = $id_usuario 
      AND status = 'prestado'
");

$total_por_vencer = safe_count_query($conn, "
    SELECT COUNT(*) FROM prestamos 
    WHERE id_usuario = $id_usuario 
      AND status = 'prestado' 
      AND fecha_devolucion IS NOT NULL 
      AND fecha_devolucion BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
");

$total_historial = safe_count_query($conn, "
    SELECT COUNT(*) FROM prestamos 
    WHERE id_usuario = $id_usuario
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHive Library - Tu Centro Digital de Lectura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ProyectoGrad/assets/css/indexstyle.css">
</head>
<body>

<header>
    <?php include 'header.php'; ?>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-slider">
        <img src="https://source.unsplash.com/random/1920x1080/?library,books" alt="Biblioteca">
    </div>
    <div class="container hero-content">
        <h1>Descubre Tu Próxima <span>Gran Lectura</span></h1>
        <p>Accede a miles de libros, e-books, audiolibros y más. Tu viaje hacia el conocimiento y la imaginación comienza aquí.</p>
        <div class="search-bar">
            <input type="text" placeholder="Buscar por título, autor o ISBN...">
            <button type="submit"><i class="fas fa-search"></i> Buscar</button>
        </div>
    </div>
</section>

<main>
    <div class="container">
        <!-- Panel de Usuario -->
        <section class="user-dashboard">
            <div class="user-avatar">
                <img src="https://source.unsplash.com/random/200x200/?portrait" alt="Avatar de usuario">
            </div>
            <div class="user-info">
                <h3>¡Bienvenido de nuevo, <?php echo htmlspecialchars($_SESSION['gmail_institucional'] ?? $nombre); ?>!</h3>
                <p><i class="fas fa-user-clock"></i> Miembro desde: 2025</p>
                <div class="user-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $total_prestados; ?></div>
                        <div class="stat-label">Libros Prestados</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $total_por_vencer; ?></div>
                        <div class="stat-label">Por Vencer</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $total_historial; ?></div>
                        <div class="stat-label">Historial de Lectura</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Aquí podrías continuar con más secciones como libros destacados, etc. -->
    </div>
</main>

<script>
    document.querySelector('.mobile-menu-btn')?.addEventListener('click', function () {
        document.querySelector('.nav-menu')?.classList.toggle('active');
    });

    document.addEventListener('click', function (event) {
        const isClickInsideNav = event.target.closest('.nav-menu');
        const isClickOnMenuBtn = event.target.closest('.mobile-menu-btn');

        if (!isClickInsideNav && !isClickOnMenuBtn && document.querySelector('.nav-menu')?.classList.contains('active')) {
            document.querySelector('.nav-menu').classList.remove('active');
        }
    });
</script>

</body>
</html>
