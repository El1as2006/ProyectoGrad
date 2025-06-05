<?php
session_start();

// Verificación de sesión
if (!isset($_SESSION['id_user'])) {
    header("Location: Admin/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHive Library - Tu Centro Digital de Lectura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ProyectoGrad/assets/css/indexstyle.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <span><img src="/ProyectoGrad/assets/images/Recurso_23.png" height="50"></span>
                </div>
                <button class="mobile-menu-btn" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="catalogo.php"><i class="fas fa-book"></i> Catálogo</a></li>
                        <li><a href="#"><i class="fas fa-bookmark"></i> Mis Libros</a></li>
                        <li><a href="#"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
                        <li><a href="#"><i class="fas fa-info-circle"></i> Acerca de</a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i> Contacto</a></li>
                        <li><a href="Admin/logout.php" class="login-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                    </ul>
                </nav>
            </div>
        </div>
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
                    <h3>¡Bienvenido de nuevo, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h3>
                    <p><i class="fas fa-user-clock"></i> Miembro desde: Enero 2023</p>
                    <div class="user-stats">
                        <div class="stat-item">
                            <div class="stat-number">3</div>
                            <div class="stat-label">Libros Prestados</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">2</div>
                            <div class="stat-label">Por Vencer</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">12</div>
                            <div class="stat-label">Historial de Lectura</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Libros Destacados -->
            <!-- ... resto de contenido permanece igual ... -->

            <!-- Footer -->
            <!-- ... permanece igual ... -->

    <script>
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        document.addEventListener('click', function(event) {
            const isClickInsideNav = event.target.closest('.nav-menu');
            const isClickOnMenuBtn = event.target.closest('.mobile-menu-btn');

            if (!isClickInsideNav && !isClickOnMenuBtn && document.querySelector('.nav-menu').classList.contains('active')) {
                document.querySelector('.nav-menu').classList.remove('active');
            }
        });
    </script>
</body>
</html>
