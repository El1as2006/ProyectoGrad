<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <style>
        .category-filter {
            text-align: center;
            margin: 20px 0;
        }
        .category-filter button {
            margin: 5px;
            padding: 8px 16px;
            border: none;
            background-color: #e0e0e0;
            color: #333;
            border-radius: 5px;
            cursor: pointer;
        }
        .category-filter button.active {
            background-color: #007bff;
            color: white;
        }
        .books-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .book-card {
            flex: 1 1 250px;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 16px;
            background: white;
            text-align: center;
        }
        .book-cover {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .book-title {
            font-size: 18px;
            font-weight: bold;
        }
        .book-author, .book-category {
            font-size: 14px;
            color: #666;
        }
        .book-description {
            font-size: 13px;
            margin-top: 10px;
        }
        .book-actions {
            margin-top: 15px;
        }
        .btn {
            padding: 6px 12px;
            margin: 0 4px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
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
                        <li><a href="Admin/logout.php" class="login-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Filtros de Categoría -->
    <div class="category-filter">
        <button class="category-btn active" data-category="todos">Todos</button>
        <?php
        $cat_query = "SELECT id, nombre FROM categorias_libros WHERE activo = 1 ORDER BY nombre ASC";
        $cat_resultado = $conexion->query($cat_query);

        $categorias_nombres = [];

        if ($cat_resultado) {
            while ($cat = $cat_resultado->fetch_assoc()) {
                $cat_id = htmlspecialchars($cat['id']);
                $cat_nombre = htmlspecialchars($cat['nombre']);
                $categorias_nombres[$cat_id] = $cat_nombre;
                echo '<button class="category-btn" data-category="' . $cat_id . '">' . $cat_nombre . '</button>';
            }
        } else {
            echo "<p style='color:red;'>Error al cargar categorías: " . $conexion->error . "</p>";
        }
        ?>
    </div>

    <!-- Sección de Libros -->
    <section class="books-section">
        <div class="container">
            <h2 class="books-title">Catálogo de Libros</h2>
            <div class="books-grid" id="booksGrid">
                <?php
                $sql = "SELECT * FROM libros ORDER BY titulo ASC";
                $resultado = $conexion->query($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    while ($libro = $resultado->fetch_assoc()) {
                        $categoria_id = htmlspecialchars($libro['categoria_id'] ?? '');
                        $categoria_nombre = $categorias_nombres[$categoria_id] ?? 'Sin Categoría';
                        $estado = htmlspecialchars($libro['estado'] ?? '');
                        $icono = htmlspecialchars($libro['icono'] ?? '📘');
                        $titulo = htmlspecialchars($libro['titulo'] ?? '');
                        $autor = htmlspecialchars($libro['autor'] ?? '');
                        $descripcion = htmlspecialchars($libro['descripcion'] ?? '');

                        echo '<div class="book-card" data-category="' . $categoria_id . '" data-status="' . $estado . '">';
                        echo '<div class="book-cover">' . $icono . '</div>';
                        echo '<div class="book-info">';
                        echo '<h3 class="book-title">' . $titulo . '</h3>';
                        echo '<p class="book-author">' . $autor . '</p>';
                        echo '<span class="book-category">' . $categoria_nombre . '</span>';
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

                $conexion->close();
                ?>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryBtns = document.querySelectorAll('.category-btn');
            const bookCards = document.querySelectorAll('.book-card');

            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const selectedCategory = this.getAttribute('data-category');

                    bookCards.forEach(card => {
                        const bookCategory = card.getAttribute('data-category');
                        if (selectedCategory === 'todos' || bookCategory === selectedCategory) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
