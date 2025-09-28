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

function safe_count_query($conn, $sql)
{
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
$libros_categoria = $conn->query("SELECT categoria_id, COUNT(*) as total FROM libros GROUP BY categoria_id");
$libros_por_categoria = $libros_categoria ? $libros_categoria->fetch_all(MYSQLI_ASSOC) : [];

// Top usuarios con más préstamos
$top_usuarios = $conn->query("SELECT u.nombre, COUNT(*) as total FROM prestamos p JOIN usuarios u ON p.id_usuario = u.id_usuario GROUP BY u.nombre ORDER BY total DESC LIMIT 5");
$usuarios_mas_prestamos = $top_usuarios ? $top_usuarios->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Colegio Salesiano Santa Cecilia</title>
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        /* Header Styles */
        .header {
            position: sticky;
            top: 0;
            z-index: 50;
            width: 100%;
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            border-bottom: 3px solid #FFD700;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            align-items: center;
            height: 64px;
            padding: 0 24px;
            gap: 24px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            padding: 2px;
        }

        .logo-text {
            color: white;
        }

        .logo-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .logo-subtitle {
            font-size: 14px;
            opacity: 0.8;
            margin: 0;
        }

        .search-container {
            flex: 1;
            max-width: 500px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: none;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 0 2px #FFD700;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .icon-button {
            background: none;
            border: none;
            color: white;
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .icon-button:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #FFD700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #000;
        }

        .user-details {
            color: white;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            margin: 0;
        }

        .user-role {
            font-size: 12px;
            opacity: 0.8;
            margin: 0;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Layout */
        .main-layout {
            display: flex;
            min-height: calc(100vh - 64px);
        }

        /* Sidebar */
        .sidebar {
            width: 256px;
            background: #2d2d2d;
            border-right: 1px solid #444;
            padding: 16px;
        }

        .nav-button {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 8px;
            border: none;
            border-radius: 8px;
            background: none;
            color: #ccc;
            text-align: left;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-button:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-button.active {
            background: #FFD700;
            color: #000;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 24px;
        }

        .welcome-section {
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .member-since {
            color: #666;
            font-size: 16px;
        }

        .welcome-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #FFD700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: #000;
            border: 4px solid #FFD700;
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #FFD700;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #FFD700;
            margin-bottom: 4px;
        }

        .stat-change {
            font-size: 12px;
            color: #666;
        }

        /* Activity Section */
        .activity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .activity-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .card-description {
            color: #666;
            font-size: 14px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #666;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .activity-time {
            font-size: 12px;
            color: #666;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-primary {
            background: #FFD700;
            color: #000;
        }

        .badge-secondary {
            background: #f0f0f0;
            color: #666;
        }

        .badge-outline {
            background: transparent;
            border: 1px solid #ddd;
            color: #666;
        }

        .action-button {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .action-button.primary {
            background: #FFD700;
            color: #000;
            font-weight: 600;
        }

        .action-button.primary:hover {
            background: #e6c200;
        }

        .action-button.outline {
            background: transparent;
            border: 1px solid #ddd;
            color: #666;
        }

        .action-button.outline:hover {
            background: #f8f9fa;
        }

        /* Icons */
        .icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .icon-lg {
            width: 20px;
            height: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                padding: 0 16px;
            }

            .logo-text {
                display: none;
            }

            .search-container {
                max-width: 200px;
            }

            .user-details {
                display: none;
            }

            .sidebar {
                width: 200px;
            }

            .activity-grid {
                grid-template-columns: 1fr;
            }

            .welcome-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }

        @media (max-width: 640px) {
            .main-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 12px;
            }

            .nav-button {
                padding: 8px 12px;
                font-size: 13px;
            }

            .main-content {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .results-box {
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            background: #fff;
            position: absolute;
            width: 300px;
            display: none;
        }

        .results-box div {
            padding: 8px;
            cursor: pointer;
        }

        .results-box div:hover {
            background: #f0f0f0;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Recurso%207-v9miyhZl7TVKTjoaZvWGS1aWqyWdk2.png"
                    alt="Colegio Salesiano Santa Cecilia" class="logo">
                <div class="logo-text">
                    <h1 class="logo-title">Colegio Salesiano</h1>
                    <p class="logo-subtitle">Santa Cecilia</p>
                </div>
            </div>

            <div class="search-container">
                <svg class="search-icon icon" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="text" id="search-input" class="search-input"
                    placeholder="Buscar por título, autor o ISBN...">
            </div>

            <!-- Contenedor donde se mostrarán los resultados -->
            <div id="search-results" class="results-box"></div>


            <?php
            header('Content-Type: application/json');
            $conn = new mysqli("localhost", "usuario", "contraseña", "base_datos");

            if ($conn->connect_error) {
                die("Error de conexión: " . $conn->connect_error);
            }

            $q = $_GET['q'] ?? "";

            if (strlen($q) > 0) {
                $stmt = $conn->prepare("SELECT titulo, autor, isbn FROM libros WHERE titulo LIKE ? OR autor LIKE ? OR isbn LIKE ? LIMIT 10");
                $like = "%" . $q . "%";
                $stmt->bind_param("sss", $like, $like, $like);
                $stmt->execute();
                $result = $stmt->get_result();

                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                echo json_encode($data);
            } else {
                echo json_encode([]);
            }
            $conn->close();
            ?>



            <div class="user-actions">
                <button class="icon-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" />
                        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" />
                    </svg>
                </button>
                <div class="user-info">
                    <div class="avatar">AP</div>
                    <div class="user-details">
                        <p class="user-name">Admin Principal</p>
                        <p class="user-role">Administrador</p>
                    </div>
                </div>
                <button class="logout-btn">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16,17 21,12 16,7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Cerrar Sesión
                </button>
            </div>
        </div>
    </header>

    <div class="main-layout">
        <aside class="sidebar">
            <nav>
                <a href="index.php" class="nav-button active">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        <polyline points="9,22 9,12 15,12 15,22" />
                    </svg>
                    Dashboard
                </a>

                <a href="list_books.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg>
                    Catálogo de libros
                </a>

                <a href="list_loans.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="8.5" cy="7" r="4" />
                        <polyline points="17,11 19,13 23,9" />
                    </svg>
                    Préstamos
                </a>

                <a href="categorias.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg>
                    Categorías
                </a>

                 <a href="subcategorias.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg>
                    Subcategorías
                </a>

                <a href="list_students.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="8.5" cy="7" r="4" />
                        <polyline points="17,11 19,13 23,9" />
                    </svg>
                    Estudiantes
                </a>

                <a href="list_users.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="8.5" cy="7" r="4" />
                        <polyline points="17,11 19,13 23,9" />
                    </svg>
                    Usuarios
                </a>

                <a href="reports.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <line x1="12" y1="20" x2="12" y2="10" />
                        <line x1="18" y1="20" x2="18" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="16" />
                    </svg>
                    Reportes
                </a>

                <a href="settings.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path
                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                    </svg>
                    Configuración
                </a>

                <a href="contact.php" class="nav-button">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                    Contacto
                </a>
            </nav>

        </aside>
        <main class="main-content">
            <div class="welcome-section">
                <div>
                    <h1 class="welcome-title">¡Bienvenido de nuevo, <?= htmlspecialchars($nombre) ?>!</h1>
                    <p class="member-since">Miembro desde: 2025</p>
                </div>
                <div class="welcome-avatar"><?= strtoupper(substr($nombre, 0, 2)) ?></div>
            </div>

            <!-- Estadísticas generales -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-title">Total Estudiantes</div>
                    <div class="stat-value"><?= $total_estudiantes ?></div>
                    <div class="stat-change">Registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total Libros</div>
                    <div class="stat-value"><?= $total_libros ?></div>
                    <div class="stat-change">En biblioteca</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total Préstamos</div>
                    <div class="stat-value"><?= $total_prestamos ?></div>
                    <div class="stat-change">Acumulado</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Préstamos Recientes</div>
                    <div class="stat-value"><?= count($ultimos_prestamos) ?></div>
                    <div class="stat-change">Últimos 5</div>
                </div>
            </div>

            <div class="activity-grid">
                <!-- Préstamos recientes -->
                <div class="activity-card">
                    <div class="card-header">
                        <div class="card-title">Préstamos Recientes</div>
                        <div class="card-description">Últimos movimientos</div>
                    </div>
                    <?php foreach ($ultimos_prestamos as $p): ?>
                        <div class="activity-item">
                            <div class="activity-avatar"><?= strtoupper(substr($p['nombre'], 0, 2)) ?></div>
                            <div class="activity-content">
                                <div class="activity-text"><?= htmlspecialchars($p['nombre']) ?> prestó
                                    "<strong><?= htmlspecialchars($p['titulo']) ?></strong>"</div>
                                <div class="activity-time"><?= date('d/m/Y H:i', strtotime($p['fecha_prestamo'])) ?></div>
                            </div>
                            <div class="badge badge-primary">Préstamo</div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Libros más prestados -->
                <div class="activity-card">
                    <div class="card-header">
                        <div class="card-title">Top Libros</div>
                        <div class="card-description">Más solicitados</div>
                    </div>
                    <?php foreach ($libros_mas_prestados as $libro): ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <div class="activity-text"><?= htmlspecialchars($libro['titulo']) ?></div>
                                <div class="activity-time"><?= $libro['total'] ?> préstamos</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Usuarios con más préstamos -->
                <div class="activity-card">
                    <div class="card-header">
                        <div class="card-title">Usuarios más activos</div>
                        <div class="card-description">Top 5 por préstamos</div>
                    </div>
                    <?php foreach ($usuarios_mas_prestamos as $usuario): ?>
                        <div class="activity-item">
                            <div class="activity-avatar"><?= strtoupper(substr($usuario['nombre'], 0, 2)) ?></div>
                            <div class="activity-content">
                                <div class="activity-text"><?= htmlspecialchars($usuario['nombre']) ?></div>
                                <div class="activity-time"><?= $usuario['total'] ?> préstamos</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Libros por categoría -->
                <div class="activity-card">
                    <div class="card-header">
                        <div class="card-title">Libros por Categoría</div>
                        <div class="card-description">Cantidad por categoría (ID)</div>
                    </div>
                    <?php foreach ($libros_por_categoria as $categoria): ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <div class="activity-text">Categoría ID <?= $categoria['categoria_id'] ?></div>
                                <div class="activity-time"><?= $categoria['total'] ?> libros</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Préstamos por mes (gráfico) -->
                <div class="activity-card" style="grid-column: span 2;">
                    <div class="card-header">
                        <div class="card-title">Préstamos por Mes</div>
                        <div class="card-description">Últimos 12 meses</div>
                    </div>
                    <canvas id="graficoPrestamos" height="100"></canvas>
                </div>
            </div>
        </main>

        <!-- Chart.js para mostrar los préstamos por mes -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('graficoPrestamos').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($prestamos_por_mes, 'mes')) ?>,
                    datasets: [{
                        label: 'Préstamos por Mes',
                        data: <?= json_encode(array_column($prestamos_por_mes, 'total')) ?>,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        </script>

    </div>

    <script>

        document.getElementById("search-input").addEventListener("keyup", function () {
            let query = this.value.trim();
            if (query.length > 0) {
                fetch("search.php?q=" + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        let resultsBox = document.getElementById("search-results");
                        resultsBox.innerHTML = "";
                        if (data.length > 0) {
                            data.forEach(item => {
                                let div = document.createElement("div");
                                div.textContent = item.titulo + " - " + item.autor + " (ISBN: " + item.isbn + ")";
                                resultsBox.appendChild(div);
                            });
                            resultsBox.style.display = "block";
                        } else {
                            resultsBox.style.display = "none";
                        }
                    });
            } else {
                document.getElementById("search-results").style.display = "none";
            }
        });




        // Search functionality
        document.querySelector('.search-input').addEventListener('input', function (e) {
            console.log('Searching for:', e.target.value);
            // Add search logic here
        });

        // Navigation functionality
        document.querySelectorAll('.nav-button').forEach(button => {
            button.addEventListener('click', function () {
                // Remove active class from all buttons
                document.querySelectorAll('.nav-button').forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                console.log('Navigating to:', this.textContent.trim());
            });
        });

        // Action buttons functionality
        document.querySelectorAll('.action-button').forEach(button => {
            button.addEventListener('click', function () {
                console.log('Action clicked:', this.textContent.trim());
                // Add action logic here
            });
        });

        // Logout functionality
        document.querySelector('.logout-btn').addEventListener('click', function () {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                console.log('Logging out...');
                // Add logout logic here
                window.location.href = 'login.php';
            }
        });

        // Notification button
        document.querySelector('.icon-button').addEventListener('click', function () {
            console.log('Notifications clicked');
            // Add notification logic here
        });
    </script>
</body>

</html>