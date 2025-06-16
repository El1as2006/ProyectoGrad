<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos (se captura el valor retornado)
$conexion = include_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Admin/login.php");
    exit;
}

$nombre = $_SESSION['user_name'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'estudiante';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ProyectoGrad/assets/css/indexstyle.css">
    <title>Catálogo - Biblioteca Chaleca</title>
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
                        <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="catalogo.php"><i class="fas fa-book"></i> Catálogo</a></li>
                        <li><a href="#"><i class="fas fa-bookmark"></i> Mis Libros</a></li>
                        <li><a href="#"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
                        <li><a href="#"><i class="fas fa-info-circle"></i> Acerca de</a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i> Contacto</a></li>
                        <li><a href="Admin/login.php" class="login-btn"><i class="fas fa-user"></i> Iniciar Sesión</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Sección de Libros -->
    <section class="books-section">
        <div class="container">
            <h2 class="books-title">Catálogo de Libros</h2>
            <div class="books-grid" id="booksGrid">
                <?php
                // Consulta para obtener los libros
                $sql = "SELECT * FROM libros";
                $resultado = $conexion->query($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    while ($libro = $resultado->fetch_assoc()) {
                        // Defensivo contra campos faltantes
                        $categoria = htmlspecialchars($libro['categoria'] ?? '');
                        $estado = htmlspecialchars($libro['estado'] ?? '');
                        $icono = htmlspecialchars($libro['icono'] ?? '');
                        $titulo = htmlspecialchars($libro['titulo'] ?? '');
                        $autor = htmlspecialchars($libro['autor'] ?? '');
                        $descripcion = htmlspecialchars($libro['descripcion'] ?? '');

                        echo '<div class="book-card" data-category="' . $categoria . '" data-status="' . $estado . '">';
                        echo '<div class="book-cover">' . $icono . '</div>';
                        echo '<div class="book-info">';
                        echo '<h3 class="book-title">' . $titulo . '</h3>';
                        echo '<p class="book-author">' . $autor . '</p>';
                        echo '<span class="book-category">' . $categoria . '</span>';
                        echo '<p class="book-description">' . $descripcion . '</p>';
                        echo '<div class="book-actions">';
                        echo '<button class="btn btn-primary">Reservar</button>';
                        echo '<button class="btn btn-secondary">Ver más</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No se encontraron libros disponibles.</p>';
                }

                // Cerrar conexión
                $conexion->close();
                ?>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const categoryCards = document.querySelectorAll('.category-card');
            const bookCards = document.querySelectorAll('.book-card');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');

                    bookCards.forEach(card => {
                        const status = card.getAttribute('data-status');
                        card.style.display = (filter === 'todos' || status === filter) ? 'block' : 'none';
                    });
                });
            });

            categoryCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const category = this.getAttribute('data-category');

                    document.querySelector('.books-section').scrollIntoView({ behavior: 'smooth' });

                    setTimeout(() => {
                        bookCards.forEach(bookCard => {
                            const bookCategory = bookCard.getAttribute('data-category');
                            if (bookCategory === category) {
                                bookCard.style.display = 'block';
                                bookCard.style.animation = 'none';
                                bookCard.offsetHeight;
                                bookCard.style.animation = 'fadeInUp 0.6s ease forwards';
                            } else {
                                bookCard.style.display = 'none';
                            }
                        });

                        filterBtns.forEach(btn => btn.classList.remove('active'));
                        filterBtns[0].classList.add('active');
                    }, 500);
                });
            });

            document.querySelectorAll('.btn-primary').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.textContent === 'Reservar') {
                        this.textContent = 'Reservado';
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-secondary');
                        this.disabled = true;
                    }
                });
            });
        });
    </script>
</body>
</html>
