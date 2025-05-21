<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHive Library - Tu Centro Digital de Lectura</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset y Estilos Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }

        :root {
            --primary: #FFD700; /* Amarillo */
            --primary-dark: #E6C200;
            --primary-light: #FFF0A0;
            --secondary: #121212; /* Negro */
            --secondary-light: #222222;
            --secondary-dark: #000000;
            --white: #FFFFFF;
            --off-white: #F8F8F8;
            --text-dark: #121212;
            --text-light: #F5F5F5;
            --gray-light: #EEEEEE;
            --gray: #888888;
            --transition: all 0.3s ease;
            --shadow: 0 4px 20px rgba(0,0,0,0.1);
            --shadow-hover: 0 8px 30px rgba(255, 215, 0, 0.2);
            --border-radius: 12px;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            background-color: var(--white);
            color: var(--secondary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Contenedor */
        .container {
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Header */
        header {
            background-color: var(--secondary);
            color: var(--white);
            padding: 1.2rem 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--primary);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: var(--primary);
            margin-left: 5px;
        }

        .logo-icon {
            font-size: 2rem;
            margin-right: 10px;
            color: var(--primary);
        }

        /* Navegación */
        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        nav ul li a {
            color: var(--white);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
            font-size: 1.05rem;
            position: relative;
            padding: 0.5rem 0;
        }

        nav ul li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: var(--transition);
        }

        nav ul li a:hover {
            color: var(--primary);
        }

        nav ul li a:hover::after {
            width: 100%;
        }

        /* Botón de Login */
        .login-btn {
            background-color: var(--primary);
            color: var(--text-dark);
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: 2px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--primary);
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .mobile-menu-btn:hover {
            transform: scale(1.1);
        }

        /* Hero Section */
        .hero {
            position: relative;
            color: var(--white);
            padding: 6rem 0;
            text-align: center;
            border-bottom: 5px solid var(--primary);
            overflow: hidden;
        }

        /* Slider de imágenes */
        .hero-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .hero-slider::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.75);
            z-index: 1;
        }

        .hero-slider img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero::before {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 30px;
            background-color: var(--primary);
            transform: rotate(45deg);
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .hero h1 span {
            color: var(--primary);
            position: relative;
            display: inline-block;
        }

        .hero h1 span::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background-color: var(--primary);
            opacity: 0.3;
            z-index: -1;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.9;
        }

        /* Barra de Búsqueda */
        .search-bar {
            background-color: var(--white);
            border-radius: 50px;
            padding: 0.6rem;
            display: flex;
            max-width: 650px;
            margin: 0 auto;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .search-bar:hover {
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-light);
        }

        .search-bar input {
            flex: 1;
            border: none;
            padding: 0.8rem 1.5rem;
            font-size: 1.1rem;
            outline: none;
            border-radius: 50px;
            background-color: var(--white);
            color: var(--secondary);
        }

        .search-bar button {
            background-color: var(--primary);
            color: var(--text-dark);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            font-size: 1.05rem;
            letter-spacing: 0.5px;
        }

        .search-bar button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Contenido Principal */
        main {
            padding: 3rem 0;
            background-color: var(--off-white);
        }

        .section-title {
            font-size: 2.2rem;
            margin-bottom: 2rem;
            color: var(--secondary);
            position: relative;
            padding-bottom: 0.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background-color: var(--primary);
            border-radius: 2px;
        }

        /* Libros Destacados */
        .featured-books {
            margin-bottom: 4rem;
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2.5rem;
        }

        .book-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid var(--gray-light);
            position: relative;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .book-cover {
            height: 320px;
            overflow: hidden;
            position: relative;
        }

        .book-cover::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: var(--primary);
        }

        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .book-card:hover .book-cover img {
            transform: scale(1.08);
        }

        .book-info {
            padding: 1.5rem;
        }

        .book-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary);
        }

        .book-author {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .book-status {
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .available {
            background-color: var(--primary);
            color: var(--text-dark);
        }

        .borrowed {
            background-color: #FF6B6B;
            color: var(--white);
        }

        /* Categorías */
        .categories {
            margin-bottom: 4rem;
        }

        .category-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .category-item {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border: 1px solid var(--gray-light);
            cursor: pointer;
        }

        .category-item:hover {
            background-color: var(--primary);
            color: var(--text-dark);
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .category-item:hover .category-icon {
            transform: scale(1.2);
        }

        .category-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Noticias y Eventos */
        .news-events {
            margin-bottom: 4rem;
        }

        .event-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            border-left: 5px solid var(--primary);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background-color: var(--primary);
            transition: var(--transition);
        }

        .event-card:hover {
            transform: translateX(5px);
            box-shadow: var(--shadow-hover);
        }

        .event-card:hover::before {
            width: 10px;
        }

        .event-date {
            color: var(--secondary);
            font-weight: 600;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            font-size: 1.05rem;
        }

        .event-date i {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        .event-title {
            font-size: 1.4rem;
            margin-bottom: 0.8rem;
            color: var(--secondary);
            font-weight: 600;
        }

        /* Panel de Usuario */
        .user-dashboard {
            background-color: var(--white);
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 2rem;
            border: 1px solid var(--gray-light);
            position: relative;
            overflow: hidden;
        }

        .user-dashboard::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: var(--primary);
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--primary);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .user-avatar:hover img {
            transform: scale(1.1);
        }

        .user-info h3 {
            margin-bottom: 0.8rem;
            color: var(--secondary);
            font-size: 1.6rem;
            font-weight: 600;
        }

        .user-info p {
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .user-stats {
            display: flex;
            gap: 2.5rem;
            margin-top: 1rem;
        }

        .stat-item {
            text-align: center;
            background-color: var(--off-white);
            padding: 1rem 1.5rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            min-width: 120px;
        }

        .stat-item:hover {
            background-color: var(--primary-light);
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.3rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--secondary);
            font-weight: 500;
        }

        /* Footer */
        footer {
            background-color: var(--secondary);
            color: var(--white);
            padding: 4rem 0 1.5rem;
            border-top: 3px solid var(--primary);
            position: relative;
        }

        footer::before {
            content: '';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            width: 30px;
            height: 30px;
            background-color: var(--primary);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-section h3 {
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.8rem;
            color: var(--primary);
            font-weight: 600;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--primary);
            border-radius: 2px;
        }

        .footer-section p {
            margin-bottom: 1.5rem;
            line-height: 1.7;
            opacity: 0.8;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
        }

        .footer-section ul li a {
            color: var(--white);
            text-decoration: none;
            transition: var(--transition);
            opacity: 0.8;
            display: flex;
            align-items: center;
        }

        .footer-section ul li a:hover {
            color: var(--primary);
            opacity: 1;
            transform: translateX(5px);
        }

        .footer-section ul li a i {
            margin-right: 0.5rem;
            color: var(--primary);
            font-size: 0.8rem;
        }

        .contact-info p {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .contact-info p i {
            margin-right: 1rem;
            color: var(--primary);
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            font-size: 1.2rem;
        }

        .social-links a:hover {
            background-color: var(--primary);
            color: var(--text-dark);
            transform: translateY(-5px);
        }

        .copyright {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.95rem;
            color: var(--gray);
        }

        /* Diseño Responsivo */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .user-dashboard {
                flex-direction: column;
                text-align: center;
                padding: 2rem 1rem;
            }
            
            .user-stats {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .stat-item {
                min-width: 100px;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                position: relative;
            }

            .mobile-menu-btn {
                display: block;
                position: absolute;
                top: 0;
                right: 0;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: var(--secondary);
                padding: 1.5rem;
                border-bottom: 3px solid var(--primary);
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                border-radius: 0 0 var(--border-radius) var(--border-radius);
            }

            .nav-menu.active {
                display: block;
            }

            nav ul {
                flex-direction: column;
                gap: 1rem;
            }

            nav ul li a {
                display: block;
                padding: 0.8rem 0;
            }

            .login-btn {
                margin-top: 1rem;
                width: 100%;
                justify-content: center;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .book-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 1.5rem;
            }
            
            .hero {
                padding: 4rem 0;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .search-bar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            .search-bar input {
                width: 100%;
                text-align: center;
            }
            
            .search-bar button {
                width: 100%;
            }
            
            .book-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .category-list {
                grid-template-columns: 1fr 1fr;
            }

            .user-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .stat-item {
                width: 100%;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .book-card, .category-item, .event-card, .user-dashboard {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .book-card:nth-child(2) { animation-delay: 0.1s; }
        .book-card:nth-child(3) { animation-delay: 0.2s; }
        .book-card:nth-child(4) { animation-delay: 0.3s; }
        
        .category-item:nth-child(2) { animation-delay: 0.1s; }
        .category-item:nth-child(3) { animation-delay: 0.2s; }
        .category-item:nth-child(4) { animation-delay: 0.3s; }
        .category-item:nth-child(5) { animation-delay: 0.4s; }
        .category-item:nth-child(6) { animation-delay: 0.5s; }
        .category-item:nth-child(7) { animation-delay: 0.6s; }
        .category-item:nth-child(8) { animation-delay: 0.7s; }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-book-open logo-icon"></i>
                    Book<span>Hive</span>
                </div>
                <button class="mobile-menu-btn" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="#"><i class="fas fa-book"></i> Catálogo</a></li>
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

    <!-- Hero Section -->
    <section class="hero">
        <!-- Slider de imágenes -->
        <div class="hero-slider">
            <!-- Aquí puedes añadir tus imágenes para el slider -->
            <img src="https://source.unsplash.com/random/1920x1080/?library,books" alt="Biblioteca">
            <!-- Puedes añadir más imágenes y controlar el slider con JavaScript -->
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
                    <h3>¡Bienvenido de nuevo, Alex!</h3>
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
            <section class="featured-books">
                <h2 class="section-title">Nuevas Adquisiciones</h2>
                <div class="book-grid">
                    <!-- Libro 1 -->
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="https://source.unsplash.com/random/400x600/?book-cover" alt="Portada del libro">
                        </div>
                        <div class="book-info">
                            <div class="book-title">La Biblioteca de Medianoche</div>
                            <div class="book-author"><i class="fas fa-pen-fancy"></i> Matt Haig</div>
                            <div class="book-status available"><i class="fas fa-check-circle"></i> Disponible</div>
                        </div>
                    </div>
                    <!-- Libro 2 -->
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="https://source.unsplash.com/random/400x600/?novel" alt="Portada del libro">
                        </div>
                        <div class="book-info">
                            <div class="book-title">Klara y el Sol</div>
                            <div class="book-author"><i class="fas fa-pen-fancy"></i> Kazuo Ishiguro</div>
                            <div class="book-status borrowed"><i class="fas fa-clock"></i> Prestado</div>
                        </div>
                    </div>
                    <!-- Libro 3 -->
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="https://source.unsplash.com/random/400x600/?fiction" alt="Portada del libro">
                        </div>
                        <div class="book-info">
                            <div class="book-title">Proyecto Hail Mary</div>
                            <div class="book-author"><i class="fas fa-pen-fancy"></i> Andy Weir</div>
                            <div class="book-status available"><i class="fas fa-check-circle"></i> Disponible</div>
                        </div>
                    </div>
                    <!-- Libro 4 -->
                    <div class="book-card">
                        <div class="book-cover">
                            <img src="https://source.unsplash.com/random/400x600/?fantasy" alt="Portada del libro">
                        </div>
                        <div class="book-info">
                            <div class="book-title">Los Cuatro Vientos</div>
                            <div class="book-author"><i class="fas fa-pen-fancy"></i> Kristin Hannah</div>
                            <div class="book-status available"><i class="fas fa-check-circle"></i> Disponible</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Categorías -->
            <section class="categories">
                <h2 class="section-title">Explorar por Categoría</h2>
                <div class="category-list">
                    <div class="category-item">
                        <div class="category-icon"><i class="fas fa-book"></i></div>
                        <div class="category-name">Ficción</div>
                    </div>
                    <div class="category-item">
                        <div class="category-icon"><i class="fas fa-search"></i></div>
                        <div class="category-name">Misterio</div>
                    </div>
                    <div class="category-item">
                        <div class="category-icon"><i class="fas fa-rocket"></i></div>
                        <div class="category-name">Ciencia Ficción</div>
                    </div>
                    <div class="category-item">
                        <div class="category-icon"><i class="fas fa-hat-wizard"></i></div>
                        <div class="category-name">Fantasía</div>
                    </div>
                    <div class="category-item">
                        <div class="category-icon"><i class="fas fa-heart"></i></div>
                        <div class="category-name">Romance</div>
                    </div>
                    <div class="category-item">
                        <div class="category-icon"><i class="fas fa-brain"></i></div>
                        <div class="category-name">Autoayuda</div>
                    </div>
                    <div class="category-item">
                        <div class="category-icon"><i class="fas fa-globe-americas"></i></div>
                        <div class="category-name">Historia</div>
                    </div>
                    <div class="category-item">
                        <div class="category-icon"><i class="fas fa-child"></i></div>
                        <div class="category-name">Infantil</div>
                    </div>
                </div>
            </section>

            <!-- Noticias y Eventos -->
            <section class="news-events">
                <h2 class="section-title">Próximos Eventos</h2>
                <div class="event-card">
                    <div class="event-date"><i class="fas fa-calendar-day"></i> 25 de Mayo, 2025 • 18:00</div>
                    <h3 class="event-title">Encuentro con la Autora: Jane Smith</h3>
                    <p>Únete a nosotros para una velada con la autora bestseller Jane Smith mientras discute su última novela "Más Allá del Horizonte".</p>
                </div>
                <div class="event-card">
                    <div class="event-date"><i class="fas fa-calendar-day"></i> 3 de Junio, 2025 • 16:30</div>
                    <h3 class="event-title">Círculo de Lectura Infantil</h3>
                    <p>Trae a tus pequeños para una tarde de cuentacuentos y actividades divertidas con nuestra bibliotecaria infantil.</p>
                </div>
                <div class="event-card">
                    <div class="event-date"><i class="fas fa-calendar-day"></i> 10 de Junio, 2025 • 17:00</div>
                    <h3 class="event-title">Club de Lectura: "El Paciente Silencioso"</h3>
                    <p>Este mes estamos discutiendo el thriller psicológico "El Paciente Silencioso" de Alex Michaelides.</p>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Sobre Nosotros</h3>
                    <p>BookHive está dedicado a fomentar el amor por la lectura y proporcionar acceso al conocimiento para todos los miembros de la comunidad.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Enlaces Rápidos</h3>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Inicio</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Catálogo</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> E-books</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Audiolibros</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Eventos</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Servicios</h3>
                    <ul>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Préstamo de Libros</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Asistencia en Investigación</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Acceso a Computadoras</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Salas de Estudio</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Impresión y Copiado</a></li>
                    </ul>
                </div>
                <div class="footer-section contact-info">
                    <h3>Contáctanos</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Avenida Lectura, Ciudad Libro</p>
                    <p><i class="fas fa-phone-alt"></i> (555) 123-4567</p>
                    <p><i class="fas fa-envelope"></i> info@bookhive.org</p>
                </div>
            </div>
            <div class="copyright">
                &copy; 2025 BookHive Library. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <script>
        // Toggle del menú móvil
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        // Cerrar menú móvil al hacer clic fuera
        document.addEventListener('click', function(event) {
            const isClickInsideNav = event.target.closest('.nav-menu');
            const isClickOnMenuBtn = event.target.closest('.mobile-menu-btn');
            
            if (!isClickInsideNav && !isClickOnMenuBtn && document.querySelector('.nav-menu').classList.contains('active')) {
                document.querySelector('.nav-menu').classList.remove('active');
            }
        });

        // Aquí puedes añadir código para controlar el slider de imágenes
        // Por ejemplo:
        /*
        const sliderImages = [
            "https://source.unsplash.com/random/1920x1080/?library",
            "https://source.unsplash.com/random/1920x1080/?books",
            "https://source.unsplash.com/random/1920x1080/?reading"
        ];
        
        let currentImageIndex = 0;
        const sliderElement = document.querySelector('.hero-slider img');
        
        function changeSliderImage() {
            currentImageIndex = (currentImageIndex + 1) % sliderImages.length;
            sliderElement.style.opacity = 0;
            
            setTimeout(() => {
                sliderElement.src = sliderImages[currentImageIndex];
                sliderElement.style.opacity = 1;
            }, 500);
        }
        
        // Cambiar imagen cada 5 segundos
        setInterval(changeSliderImage, 5000);
        */
    </script>
</body>
</html>