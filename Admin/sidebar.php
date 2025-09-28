<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 240px;
        height: 100vh;
        background: #fff;
        border-right: 1px solid #ddd;
        padding: 20px 10px;
    }

    .nav-button {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        color: #333;
        text-decoration: none;
        border-radius: 8px;
        transition: background 0.2s;
    }

    .nav-button:hover {
        background: #f0f0f0;
    }

    .nav-button.active {
        background: #007bff;
        color: #fff;
    }

    .icon {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }
</style>

<!-- sidebar.php -->
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

        <!-- <a href="reports.php" class="nav-button">
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
        </a> -->
    </nav>
</aside>

<style>
    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 65px;
        /* lo empujamos debajo del header */
        left: 0;
        width: 240px;
        height: calc(100vh - 65px);
        /* para que no se solape con el header */
        background: #000;
        padding: 20px 10px;
    }


    /* Botones */
    .nav-button {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        transition: background 0.2s;
    }

    .nav-button:hover {
        background: #333;
    }

    .nav-button.active {
        background: #FFD700;
        color: #fff;
    }

    /* Íconos */
    .icon {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    /* Para que el contenido no quede debajo del sidebar */
    body {
        margin-left: 240px;
    }
</style>