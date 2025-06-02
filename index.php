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
                    <span><img src="/ProyectoGrad/assets/images/Recurso_23.png"  height="50"></span>
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
                        <li><a href="catalogo.php"><i class="fas fa-chevron-right"></i> Catálogo</a></li>
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