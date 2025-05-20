<nav class="sidebar" style="background-color: white;">
    <div class="logo d-flex justify-content-between">
        <a href="index.php"><img src="./img/logo_chaleco.png" alt="Logo"></a>
        <div class="sidebar_close_icon d-lg-none">
            <i class="ti-close"></i>
        </div>
    </div>
    <ul id="sidebar_menu">
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'estudiante'): ?>
            <li class="mm-active">
                <a href="index.php" aria-expanded="false">
                    <img src="./img/menu-icon/1.svg" alt="Dashboard Icon">
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="libros_disponibles.php" aria-expanded="false">
                    <img src="./img/menu-icon/3.svg" alt="Available Books Icon">
                    <span>Libros Disponibles</span>
                </a>
            </li>
            <li>
                <a href="historial_prestamos_estudiante.php" aria-expanded="false">
                    <img src="./img/menu-icon/3.svg" alt="Loan History Icon">
                    <span>Historial de Préstamos</span>
                </a>
            </li>
        <?php else: ?>
            <li class="mm-active">
                <a href="index.php" aria-expanded="false">
                    <img src="./img/menu-icon/1.svg" alt="Dashboard Icon">
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a class="has-arrow" href="#" aria-expanded="false">
                    <img src="./img/menu-icon/2.svg" alt="Students Icon">
                    <span>Estudiantes</span>
                </a>
                <ul>
                    <li><a href="añadir_estudiante.php">Añadir estudiante</a></li>
                    <li><a href="historial_estudiantes.php">Historial de estudiantes</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="#" aria-expanded="false">
                    <img src="./img/menu-icon/3.svg" alt="Books Icon">
                    <span>Libros</span>
                </a>
                <ul>
                    <li><a href="libros.php">Añadir libros</a></li>
                    <li><a href="historial_libros.php">Historial de libros</a></li>
                    <li><a href="libros_disponibles.php">Libros disponibles</a></li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="#" aria-expanded="false">
                    <img src="./img/menu-icon/3.svg" alt="Loans Icon">
                    <span>Prestamos</span>
                </a>
                <ul>
                    <li><a href="prestamo_libros.php">Realizar préstamo libro</a></li>
                    <li><a href="historial_prestamos.php">Historial de préstamos</a></li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</nav>