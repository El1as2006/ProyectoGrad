<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$id_usuario = $_SESSION['user_id'] ?? null;
$user_rol = $_SESSION['user_rol'] ?? '';

if ($user_rol === 'admin' || $user_rol === 'super_admin') {
    $query = "
        SELECT prestamos.*, libros.titulo
        FROM prestamos
        JOIN libros ON prestamos.id_libro = libros.id
        ORDER BY prestamos.id_prestamo DESC
    ";
} else {
    $query = "
        SELECT prestamos.*, libros.titulo
        FROM prestamos
        JOIN libros ON prestamos.id_libro = libros.id
        WHERE prestamos.id_usuario = $id_usuario
        ORDER BY prestamos.id_prestamo DESC
    ";
}

$result = $conn->query($query);
$prestamos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_usuario_prestamo = $row['id_usuario'];

        // Primero busca en estudiantes
        $estudiante_query = $conn->query("SELECT nombre, gmail_institucional FROM estudiantes WHERE id = $id_usuario_prestamo LIMIT 1");
        if ($estudiante_query && $estudiante_query->num_rows > 0) {
            $estudiante = $estudiante_query->fetch_assoc();
            $row['nombre'] = $estudiante['nombre'];
            $row['gmail_institucional'] = $estudiante['gmail_institucional'];
        } else {
            // Si no es estudiante, busca en usuarios
            $usuario_query = $conn->query("SELECT nombre, gmail_institucional FROM usuarios WHERE id_usuario = $id_usuario_prestamo LIMIT 1");
            if ($usuario_query && $usuario_query->num_rows > 0) {
                $usuario = $usuario_query->fetch_assoc();
                $row['nombre'] = $usuario['nombre'];
                $row['gmail_institucional'] = $usuario['gmail_institucional'];
            } else {
                // Si no está en ninguna tabla
                $row['nombre'] = 'Desconocido';
                $row['gmail_institucional'] = 'Desconocido';
            }
        }
        $prestamos[] = $row;
    }
} else {
    echo '<div class="alert alert-warning">No se encontraron préstamos.</div>';
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Préstamos - Colegio Salesiano Santa Cecilia</title>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --background: #FFFFFF;
            --foreground: #000000;
            --card: #F8F9FA;
            --card-foreground: #000000;
            --primary: #FFC107;
            --primary-foreground: #FFFFFF;
            --secondary: #F8F9FA;
            --secondary-foreground: #000000;
            --muted: #F8F9FA;
            --muted-foreground: #6B7280;
            --accent: #FFC107;
            --accent-foreground: #000000;
            --destructive: #DC3545;
            --destructive-foreground: #FFFFFF;
            --border: #E0E0E0;
            --input: #FFFFFF;
            --ring: rgba(255, 193, 7, 0.3);
            --radius: 0.5rem;
            --sidebar: #1A1A1A;
            --sidebar-foreground: #FFFFFF;
            --sidebar-primary: #FFC107;
            --sidebar-primary-foreground: #000000;
            --success: #28A745;
            --warning: #FFC107;
            --danger: #DC3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--background);
            color: var(--foreground);
            line-height: 1.6;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: var(--background);
            border-bottom: 3px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            width: 50px;
            height: 50px;
            background: var(--foreground);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--background);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .search-section {
            flex: 1;
            max-width: 500px;
            margin: 0 2rem;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--ring);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted-foreground);
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-foreground);
            font-weight: 600;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar);
            color: var(--sidebar-foreground);
            padding-top: 70px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .nav-menu {
            padding: 2rem 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 2rem;
            color: var(--sidebar-foreground);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .nav-item:hover {
            background: rgba(255, 193, 7, 0.1);
            border-left-color: var(--primary);
        }

        .nav-item.active {
            background: var(--primary);
            color: var(--primary-foreground);
            border-left-color: var(--primary);
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding-top: 70px;
            background: var(--background);
        }

        .content-wrapper {
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border);
        }

        .page-title {
            font-family: 'Work Sans', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--foreground);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .filter-dropdown {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            background: var(--background);
            color: var(--foreground);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-dropdown:hover {
            border-color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--primary-foreground);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            background: #E6AC00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card);
            padding: 1.5rem;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stat-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.active { background: var(--success); }
        .stat-icon.pending { background: var(--warning); }
        .stat-icon.overdue { background: var(--danger); }
        .stat-icon.total { background: var(--primary); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--foreground);
        }

        .stat-label {
            color: var(--muted-foreground);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* Data Table */
        .table-container {
            background: var(--card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: var(--primary);
            color: var(--primary-foreground);
            padding: 1rem;
            text-align: left;
            font-family: 'Work Sans', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(255, 193, 7, 0.05);
        }

        .table tbody tr:nth-child(even) {
            background: rgba(248, 249, 250, 0.5);
        }

        .student-info {
            display: flex;
            flex-direction: column;
        }

        .student-name {
            font-weight: 600;
            color: var(--foreground);
            margin-bottom: 0.25rem;
        }

        .student-email {
            color: var(--muted-foreground);
            font-size: 0.85rem;
        }

        .book-title {
            font-weight: 600;
            color: var(--foreground);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.returned {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-badge.pending {
            background: rgba(255, 193, 7, 0.1);
            color: #B8860B;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-badge.overdue {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .action-btn {
            background: #17A2B8;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: #138496;
            transform: translateY(-1px);
        }

        .action-btn:disabled {
            background: var(--muted-foreground);
            cursor: not-allowed;
            transform: none;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            padding: 1rem;
        }

        .pagination-btn {
            background: var(--background);
            border: 2px solid var(--border);
            color: var(--foreground);
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination-btn:hover {
            border-color: var(--primary);
            background: var(--primary);
            color: var(--primary-foreground);
        }

        .pagination-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--primary-foreground);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .header {
                padding: 0 1rem;
            }

            .search-section {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 800px;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        /* Tooltips */
        .tooltip {
            position: relative;
            cursor: help;
        }

        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--foreground);
            color: var(--background);
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .tooltip:hover::after {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="header">
            <div class="logo-section">
                <div class="logo">SC</div>
                <button class="mobile-menu-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="search-section">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar préstamos, estudiantes, libros...">
            </div>
            
            <div class="user-section">
                <i class="fas fa-bell" style="font-size: 1.2rem; color: var(--muted-foreground);"></i>
                <i class="fas fa-cog" style="font-size: 1.2rem; color: var(--muted-foreground);"></i>
                <div class="user-avatar">AP</div>
                <div>
                    <div style="font-weight: 600; font-size: 0.9rem;">Admin Principal</div>
                    <div style="color: var(--muted-foreground); font-size: 0.8rem;">Usuario</div>
                </div>
            </div>
        </header>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="nav-menu">
                <a href="dashboard.html" class="nav-item">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Estudiantes</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-book"></i>
                    <span>Libros</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-tags"></i>
                    <span>Categorías</span>
                </a>
                <a href="prestamos.html" class="nav-item active">
                    <i class="fas fa-handshake"></i>
                    <span>Préstamos</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-user-cog"></i>
                    <span>Usuarios</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-bell"></i>
                    <span>Notificaciones</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Page Header -->
                <div class="page-header fade-in">
                    <h1 class="page-title">Gestión de Préstamos</h1>
                    <div class="header-actions">
                        <select class="filter-dropdown">
                            <option value="all">Todos los préstamos</option>
                            <option value="active">Préstamos activos</option>
                            <option value="returned">Devueltos</option>
                            <option value="overdue">Vencidos</option>
                        </select>
                        <button class="btn-primary" onclick="openNewLoanModal()">
                            <i class="fas fa-plus"></i>
                            Registrar Préstamo
                        </button>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid fade-in">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon total">
                                <i class="fas fa-handshake"></i>
                            </div>
                        </div>
                        <div class="stat-number">156</div>
                        <div class="stat-label">Total Préstamos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon active">
                                <i class="fas fa-book-open"></i>
                            </div>
                        </div>
                        <div class="stat-number">89</div>
                        <div class="stat-label">Préstamos Activos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon pending">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="stat-number">12</div>
                        <div class="stat-label">Por Vencer</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon overdue">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="stat-number">5</div>
                        <div class="stat-label">Vencidos</div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-container fade-in">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante/Usuario</th>
                                <th>Libro</th>
                                <th>Fecha Préstamo</th>
                                <th>Fecha Devolución</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>17</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-name">Carlos Alfonso Torres Argueta</div>
                                        <div class="student-email">ct198316@gmail.com</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="book-title">Dune</div>
                                </td>
                                <td>2025-06-23</td>
                                <td>2025-06-24</td>
                                <td>
                                    <span class="status-badge overdue tooltip" data-tooltip="Préstamo vencido">
                                        <i class="fas fa-times-circle"></i>
                                        Vencido
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn" onclick="markAsReturned(17)">
                                        <i class="fas fa-check"></i> Marcar Devuelto
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>16</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-name">Luis Manuel Ramos Herrera</div>
                                        <div class="student-email">luisravanzo25@gmail.com</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="book-title">Dune</div>
                                </td>
                                <td>2025-06-23</td>
                                <td>2025-06-23</td>
                                <td>
                                    <span class="status-badge returned tooltip" data-tooltip="Libro devuelto correctamente">
                                        <i class="fas fa-check-circle"></i>
                                        Devuelto
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn" disabled>
                                        <i class="fas fa-check"></i> Completado
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>15</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-name">Luis Manuel Ramos Herrera</div>
                                        <div class="student-email">luisravanzo25@gmail.com</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="book-title">Dune</div>
                                </td>
                                <td>2025-06-23</td>
                                <td>2025-06-23</td>
                                <td>
                                    <span class="status-badge returned tooltip" data-tooltip="Libro devuelto correctamente">
                                        <i class="fas fa-check-circle"></i>
                                        Devuelto
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn" disabled>
                                        <i class="fas fa-check"></i> Completado
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>14</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-name">Luis Manuel Ramos Herrera</div>
                                        <div class="student-email">luisravanzo24@gmail.com</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="book-title">Dune</div>
                                </td>
                                <td>2025-06-23</td>
                                <td>2025-06-23</td>
                                <td>
                                    <span class="status-badge returned tooltip" data-tooltip="Libro devuelto correctamente">
                                        <i class="fas fa-check-circle"></i>
                                        Devuelto
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn" disabled>
                                        <i class="fas fa-check"></i> Completado
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>13</strong></td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-name">María Elena Vásquez Cruz</div>
                                        <div class="student-email">maria.vasquez@estudiante.edu</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="book-title">El Principito</div>
                                </td>
                                <td>2025-06-20</td>
                                <td>2025-06-27</td>
                                <td>
                                    <span class="status-badge pending tooltip" data-tooltip="Préstamo activo, vence pronto">
                                        <i class="fas fa-clock"></i>
                                        Activo
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn" onclick="markAsReturned(13)">
                                        <i class="fas fa-check"></i> Marcar Devuelto
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <button class="pagination-btn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <button class="pagination-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }

        // Mark loan as returned
        function markAsReturned(loanId) {
            if (confirm('¿Está seguro de marcar este préstamo como devuelto?')) {
                // Here you would typically make an API call
                console.log(`[v0] Marking loan ${loanId} as returned`);
                
                // Update UI (for demo purposes)
                const row = document.querySelector(`tr:has(td:first-child strong:contains('${loanId}'))`);
                if (row) {
                    const statusCell = row.querySelector('.status-badge');
                    const actionCell = row.querySelector('.action-btn');
                    
                    statusCell.className = 'status-badge returned tooltip';
                    statusCell.setAttribute('data-tooltip', 'Libro devuelto correctamente');
                    statusCell.innerHTML = '<i class="fas fa-check-circle"></i> Devuelto';
                    
                    actionCell.disabled = true;
                    actionCell.innerHTML = '<i class="fas fa-check"></i> Completado';
                }
                
                // Update statistics (demo)
                updateStatistics();
            }
        }

        // Open new loan modal (placeholder)
        function openNewLoanModal() {
            alert('Funcionalidad de nuevo préstamo - Aquí se abriría un modal para registrar un nuevo préstamo');
        }

        // Update statistics
        function updateStatistics() {
            // This would typically fetch real data from an API
            console.log('[v0] Updating loan statistics');
        }

        // Filter functionality
        document.querySelector('.filter-dropdown').addEventListener('change', function(e) {
            const filterValue = e.target.value;
            console.log(`[v0] Filtering loans by: ${filterValue}`);
            // Here you would implement the actual filtering logic
        });

        // Search functionality
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            console.log(`[v0] Searching for: ${searchTerm}`);
            // Here you would implement the search logic
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[v0] Loans management page loaded');
            
            // Add fade-in animation to elements
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !menuBtn.contains(e.target) && 
                sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    </script>
</body>
</html>
