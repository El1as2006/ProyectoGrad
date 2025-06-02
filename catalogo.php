<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ProyectoGrad/assets/css/indexstyle.css">

    <title>Catálogo - Bilioteca Chaleca</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #000;
            background-color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #000 0%, #333 100%);
            color: #fff;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            border-bottom: 4px solid #FFD700;
        }

        header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 300;
            color: #FFD700;
        }

        header p {
            font-size: 1.1rem;
            opacity: 0.9;
            color: #fff;
        }

        /* Categorías */
        .categories-section {
            background: #fff;
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-top: 2px solid #FFD700;
        }

        .categories-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: #000;
            font-weight: 300;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .category-card {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #000;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            position: relative;
            overflow: hidden;
            border: 2px solid #000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .category-card:nth-child(2n) { 
            background: linear-gradient(135deg, #000 0%, #333 100%); 
            color: #FFD700;
            border: 2px solid #FFD700;
        }

        .category-card:nth-child(3n) { 
            background: linear-gradient(135deg, #fff 0%, #f5f5f5 100%); 
            color: #000;
            border: 2px solid #000;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .category-card:hover::before {
            left: 100%;
        }

        .category-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .category-card .icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        /* Filtros */
        .filters {
            text-align: center;
            margin-bottom: 2rem;
        }

        .filter-btn {
            background: #000;
            color: #FFD700;
            border: 2px solid #FFD700;
            padding: 0.8rem 1.5rem;
            margin: 0.5rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .filter-btn:hover, .filter-btn.active {
            background: #FFD700;
            color: #000;
            border: 2px solid #000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Sección de libros */
        .books-section {
            padding: 2rem 0;
            background: #f9f9f9;
        }

        .books-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: #000;
            font-weight: 300;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .book-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid #000;
        }

        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            border-color: #FFD700;
        }

        .book-cover {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, #000 0%, #333 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFD700;
            font-size: 3rem;
            position: relative;
            overflow: hidden;
            border-bottom: 3px solid #FFD700;
        }

        .book-cover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23FFD700" opacity="0.1"/><rect x="10" y="10" width="80" height="80" fill="none" stroke="%23FFD700" stroke-width="2" opacity="0.3"/></svg>');
        }

        .book-info {
            padding: 1.5rem;
        }

        .book-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #000;
            line-height: 1.3;
        }

        .book-author {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .book-category {
            display: inline-block;
            background: #FFD700;
            color: #000;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #000;
            font-weight: 500;
        }

        .book-description {
            color: #333;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 1rem;
        }

        .book-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: 2px solid #000;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            flex: 1;
            font-weight: 500;
        }

        .btn-primary {
            background: #000;
            color: #FFD700;
        }

        .btn-primary:hover {
            background: #FFD700;
            color: #000;
        }

        .btn-secondary {
            background: #fff;
            color: #000;
            border: 2px solid #000;
        }

        .btn-secondary:hover {
            background: #000;
            color: #fff;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }

        /* Estados especiales */
        .book-card[data-status="prestado"] .book-cover {
            background: linear-gradient(135deg, #666 0%, #999 100%);
            color: #fff;
        }

        .book-card[data-status="prestado"] .btn-secondary {
            background: #666;
            color: #fff;
            border-color: #666;
        }

        .book-card[data-status="nuevo"] {
            border-color: #FFD700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }

        .book-card[data-status="nuevo"]::before {
            content: 'NUEVO';
            position: absolute;
            top: 10px;
            right: -30px;
            background: #FFD700;
            color: #000;
            padding: 5px 40px;
            font-size: 0.8rem;
            font-weight: bold;
            transform: rotate(45deg);
            z-index: 10;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 1rem;
            }

            .books-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
            }

            header h1 {
                font-size: 2rem;
            }

            .categories-title, .books-title {
                font-size: 1.5rem;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .book-card {
            animation: fadeInUp 0.6s ease forwards;
            position: relative;
        }

        .book-card:nth-child(even) {
            animation-delay: 0.1s;
        }

        /* Efectos adicionales */
        .category-card:hover .icon {
            transform: scale(1.2);
            transition: transform 0.3s ease;
        }

        /* Scroll suave */
        html {
            scroll-behavior: smooth;
        }
    </style>
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

    <!-- Sección de Categorías -->
    <section class="categories-section">
        <div class="container">
            <h2 class="categories-title">Explorar por Categoría</h2>
            <div class="categories-grid">
                <a href="#" class="category-card" data-category="ficcion">
                    <div class="icon">📖</div>
                    <h3>Ficción</h3>
                </a>
                <a href="#" class="category-card" data-category="misterio">
                    <div class="icon">🔍</div>
                    <h3>Misterio</h3>
                </a>
                <a href="#" class="category-card" data-category="ciencia-ficcion">
                    <div class="icon">🚀</div>
                    <h3>Ciencia Ficción</h3>
                </a>
                <a href="#" class="category-card" data-category="fantasia">
                    <div class="icon">🧙‍♂️</div>
                    <h3>Fantasía</h3>
                </a>
                <a href="#" class="category-card" data-category="romance">
                    <div class="icon">💕</div>
                    <h3>Romance</h3>
                </a>
                <a href="#" class="category-card" data-category="autoayuda">
                    <div class="icon">💪</div>
                    <h3>Autoayuda</h3>
                </a>
                <a href="#" class="category-card" data-category="historia">
                    <div class="icon">🏛️</div>
                    <h3>Historia</h3>
                </a>
                <a href="#" class="category-card" data-category="infantil">
                    <div class="icon">🧸</div>
                    <h3>Infantil</h3>
                </a>
                <a href="#" class="category-card" data-category="religion">
                    <div class="icon">🙏</div>
                    <h3>Religión</h3>
                </a>
            </div>

            <!-- Filtros -->
            <div class="filters">
                <button class="filter-btn active" data-filter="todos">Todos los libros</button>
                <button class="filter-btn" data-filter="disponible">Disponibles</button>
                <button class="filter-btn" data-filter="prestado">Prestados</button>
                <button class="filter-btn" data-filter="nuevo">Nuevos</button>
            </div>
        </div>
    </section>

    <!-- Sección de Libros -->
    <section class="books-section">
        <div class="container">
            <h2 class="books-title">Catálogo de Libros</h2>
            <div class="books-grid" id="booksGrid">
                <!-- Libros de Ficción -->
                <div class="book-card" data-category="ficcion" data-status="disponible">
                    <div class="book-cover">📚</div>
                    <div class="book-info">
                        <h3 class="book-title">Cien años de soledad</h3>
                        <p class="book-author">Gabriel García Márquez</p>
                        <span class="book-category">Ficción</span>
                        <p class="book-description">Una obra maestra del realismo mágico que narra la historia de la familia Buendía.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <div class="book-card" data-category="ficcion" data-status="prestado">
                    <div class="book-cover">📖</div>
                    <div class="book-info">
                        <h3 class="book-title">1984</h3>
                        <p class="book-author">George Orwell</p>
                        <span class="book-category">Ficción</span>
                        <p class="book-description">Una distopía que explora temas de totalitarismo y vigilancia.</p>
                        <div class="book-actions">
                            <button class="btn btn-secondary">En préstamo</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <!-- Libros de Misterio -->
                <div class="book-card" data-category="misterio" data-status="disponible">
                    <div class="book-cover">🔍</div>
                    <div class="book-info">
                        <h3 class="book-title">El nombre de la rosa</h3>
                        <p class="book-author">Umberto Eco</p>
                        <span class="book-category">Misterio</span>
                        <p class="book-description">Un misterio medieval ambientado en una abadía benedictina.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <div class="book-card" data-category="misterio" data-status="nuevo">
                    <div class="book-cover">🕵️</div>
                    <div class="book-info">
                        <h3 class="book-title">Los crímenes de la calle Morgue</h3>
                        <p class="book-author">Edgar Allan Poe</p>
                        <span class="book-category">Misterio</span>
                        <p class="book-description">El primer relato de detectives de la literatura moderna.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <!-- Libros de Ciencia Ficción -->
                <div class="book-card" data-category="ciencia-ficcion" data-status="disponible">
                    <div class="book-cover">🚀</div>
                    <div class="book-info">
                        <h3 class="book-title">Dune</h3>
                        <p class="book-author">Frank Herbert</p>
                        <span class="book-category">Ciencia Ficción</span>
                        <p class="book-description">Una épica espacial en el planeta desértico Arrakis.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <div class="book-card" data-category="ciencia-ficcion" data-status="disponible">
                    <div class="book-cover">🌌</div>
                    <div class="book-info">
                        <h3 class="book-title">Fundación</h3>
                        <p class="book-author">Isaac Asimov</p>
                        <span class="book-category">Ciencia Ficción</span>
                        <p class="book-description">La saga que revolucionó la ciencia ficción moderna.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <!-- Libros de Fantasía -->
                <div class="book-card" data-category="fantasia" data-status="prestado">
                    <div class="book-cover">🧙‍♂️</div>
                    <div class="book-info">
                        <h3 class="book-title">El Señor de los Anillos</h3>
                        <p class="book-author">J.R.R. Tolkien</p>
                        <span class="book-category">Fantasía</span>
                        <p class="book-description">La épica aventura en la Tierra Media.</p>
                        <div class="book-actions">
                            <button class="btn btn-secondary">En préstamo</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <div class="book-card" data-category="fantasia" data-status="nuevo">
                    <div class="book-cover">⚔️</div>
                    <div class="book-info">
                        <h3 class="book-title">Juego de Tronos</h3>
                        <p class="book-author">George R.R. Martin</p>
                        <span class="book-category">Fantasía</span>
                        <p class="book-description">Intriga política en un mundo de fantasía medieval.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <!-- Libros de Romance -->
                <div class="book-card" data-category="romance" data-status="disponible">
                    <div class="book-cover">💕</div>
                    <div class="book-info">
                        <h3 class="book-title">Orgullo y Prejuicio</h3>
                        <p class="book-author">Jane Austen</p>
                        <span class="book-category">Romance</span>
                        <p class="book-description">Una historia de amor clásica en la Inglaterra del siglo XIX.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <!-- Libros de Autoayuda -->
                <div class="book-card" data-category="autoayuda" data-status="disponible">
                    <div class="book-cover">💪</div>
                    <div class="book-info">
                        <h3 class="book-title">Los 7 hábitos de la gente altamente efectiva</h3>
                        <p class="book-author">Stephen Covey</p>
                        <span class="book-category">Autoayuda</span>
                        <p class="book-description">Principios fundamentales para el desarrollo personal y profesional.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <!-- Libros de Historia -->
                <div class="book-card" data-category="historia" data-status="nuevo">
                    <div class="book-cover">🏛️</div>
                    <div class="book-info">
                        <h3 class="book-title">Sapiens</h3>
                        <p class="book-author">Yuval Noah Harari</p>
                        <span class="book-category">Historia</span>
                        <p class="book-description">Una breve historia de la humanidad desde sus orígenes.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <!-- Libros Infantiles -->
                <div class="book-card" data-category="infantil" data-status="disponible">
                    <div class="book-cover">🧸</div>
                    <div class="book-info">
                        <h3 class="book-title">El Principito</h3>
                        <p class="book-author">Antoine de Saint-Exupéry</p>
                        <span class="book-category">Infantil</span>
                        <p class="book-description">Una hermosa fábula sobre la amistad y la imaginación.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>

                <!-- Libros de Religión -->
                <div class="book-card" data-category="religion" data-status="disponible">
                    <div class="book-cover">🙏</div>
                    <div class="book-info">
                        <h3 class="book-title">Meditaciones</h3>
                        <p class="book-author">Marco Aurelio</p>
                        <span class="book-category">Religión</span>
                        <p class="book-description">Reflexiones filosóficas y espirituales del emperador romano.</p>
                        <div class="book-actions">
                            <button class="btn btn-primary">Reservar</button>
                            <button class="btn btn-secondary">Ver más</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Funcionalidad de filtros
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const categoryCards = document.querySelectorAll('.category-card');
            const bookCards = document.querySelectorAll('.book-card');

            // Filtros por estado
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover clase active de todos los botones
                    filterBtns.forEach(b => b.classList.remove('active'));
                    // Agregar clase active al botón clickeado
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');
                    
                    bookCards.forEach(card => {
                        if (filter === 'todos') {
                            card.style.display = 'block';
                        } else {
                            const status = card.getAttribute('data-status');
                            if (status === filter) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        }
                    });
                });
            });

            // Filtros por categoría
            categoryCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const category = this.getAttribute('data-category');
                    
                    // Scroll a la sección de libros
                    document.querySelector('.books-section').scrollIntoView({
                        behavior: 'smooth'
                    });

                    // Filtrar libros por categoría
                    setTimeout(() => {
                        bookCards.forEach(bookCard => {
                            const bookCategory = bookCard.getAttribute('data-category');
                            if (bookCategory === category) {
                                bookCard.style.display = 'block';
                                bookCard.style.animation = 'none';
                                bookCard.offsetHeight; // Trigger reflow
                                bookCard.style.animation = 'fadeInUp 0.6s ease forwards';
                            } else {
                                bookCard.style.display = 'none';
                            }
                        });

                        // Resetear filtros de estado
                        filterBtns.forEach(btn => btn.classList.remove('active'));
                        filterBtns[0].classList.add('active');
                    }, 500);
                });
            });

            // Funcionalidad de botones de libros
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