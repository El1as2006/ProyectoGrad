<?php
session_start();
$conexion = include_once 'conexion.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: Admin/login.php");
    exit;
}

$nombre = $_SESSION['user_name'] ?? '';
$rol = $_SESSION['user_rol'] ?? 'estudiante';

// Cargar categorías y libros
$cat_res = $conexion->query("SELECT id, nombre FROM categorias_libros WHERE activo=1 ORDER BY nombre");
$categorias = [];
while ($cat = $cat_res->fetch_assoc()) {
    $categorias[$cat['id']] = $cat['nombre'];
}
$libros_res = $conexion->query("SELECT * FROM libros ORDER BY titulo");
$libros = [];
while ($libro = $libros_res->fetch_assoc()) {
    $libros[] = $libro;
}

$sel_cat_id = isset($_GET['cat']) ? intval($_GET['cat']) : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Catálogo – Biblioteca Chaleca</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/ProyectoGrad/assets/css/indexstyle.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      color: #333;
    }

    header {
      background-color: #111;
      padding: 15px 0;
      border-bottom: 3px solid #ffcc00;
      margin: 0;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 15px;
    }

    .header-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    nav.nav-menu ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 20px;
      align-items: center;
    }

    nav.nav-menu ul li a {
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: color 0.3s ease;
    }

    nav.nav-menu ul li a:hover,
    nav.nav-menu ul li a.login-btn {
      color: #ffcc00;
    }

    .mobile-menu-btn {
      background: none;
      border: none;
      color: #fff;
      font-size: 24px;
      cursor: pointer;
      display: none;
    }

    @media (max-width: 768px) {
      .mobile-menu-btn {
        display: block;
      }

      nav.nav-menu {
        position: absolute;
        top: 65px;
        left: 0;
        right: 0;
        background-color: #111;
        flex-direction: column;
        display: none;
        padding: 10px 0;
        border-top: 3px solid #ffcc00;
        z-index: 100;
      }

      nav.nav-menu.active {
        display: flex;
      }

      nav.nav-menu ul {
        flex-direction: column;
        gap: 10px;
      }
    }

    main.container {
      padding: 20px 15px 40px;
    }

    h2.section-title {
      font-size: 28px;
      text-align: center;
      margin: 30px 0 20px;
      border-bottom: 3px solid #ffcc00;
      display: inline-block;
      padding-bottom: 6px;
    }

    .back-link {
      display: inline-block;
      margin: 20px 0;
      color: #007bff;
      text-decoration: none;
      font-weight: 600;
    }

    .back-link:hover {
      color: #0056b3;
    }

    .grid {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      justify-content: center;
      margin-top: 20px;
    }

    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      padding: 20px;
      text-align: center;
      flex: 1 1 220px;
      max-width: 250px;
      transition: transform .3s ease, box-shadow .3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12);
    }

    .card-icon {
      font-size: 42px;
      color: #007bff;
      margin-bottom: 12px;
    }

    .card-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #222;
    }

    .card-sub {
      font-size: 14px;
      color: #666;
      margin-bottom: 12px;
    }

    .btn {
      display: inline-block;
      padding: 8px 16px;
      background: #007bff;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: background .3s;
    }

    .btn:hover {
      background: #0056b3;
    }

    @media (max-width: 768px) {
      .card {
        flex: 1 1 45%;
      }
    }

    @media (max-width: 480px) {
      .card {
        flex: 1 1 100%;
      }

      nav.nav-menu ul {
        flex-direction: column;
        gap: 10px;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="container">
    <div class="header-content">
      <div class="logo">
        <span><img src="/ProyectoGrad/assets/images/Recurso_23.png" height="50" alt="Logo"></span>
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

<main class="container">
  <?php if (!$sel_cat_id): ?>
    <h2 class="section-title">Categorías</h2>
    <div class="grid">
      <?php foreach ($categorias as $id => $nombre): ?>
        <div class="card">
          <div class="card-icon">📚</div>
          <div class="card-title"><?php echo htmlspecialchars($nombre); ?></div>
          <a href="catalogo.php?cat=<?php echo $id; ?>" class="btn">Ver libros</a>
        </div>
      <?php endforeach; ?>
    </div>

  <?php else:
    if (!isset($categorias[$sel_cat_id])) { header("Location: catalogo.php"); exit; }
    $catNombre = $categorias[$sel_cat_id];
  ?>
    <a href="catalogo.php" class="back-link">← Volver a categorías</a>
    <h2 class="section-title"><?php echo htmlspecialchars($catNombre); ?></h2>
    <div class="grid">
      <?php $count = 0;
      foreach ($libros as $libro):
        if ((int)$libro['categoria_id'] === $sel_cat_id): $count++; ?>
          <div class="card">
            <div class="card-icon">📘</div>
            <div class="card-title"><?php echo htmlspecialchars($libro['titulo']); ?></div>
            <div class="card-sub"><?php echo htmlspecialchars($libro['autor']); ?></div>
            <a href="libro_detalle.php?id=<?php echo $libro['id']; ?>" class="btn">Ver detalles</a>
          </div>
      <?php endif; endforeach;
      if (!$count): ?>
        <p>No hay libros en esta categoría.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</main>

<script>
  document.querySelector('.mobile-menu-btn').addEventListener('click', function () {
    document.querySelector('.nav-menu').classList.toggle('active');
  });

  document.addEventListener('click', function (event) {
    const isClickInsideNav = event.target.closest('.nav-menu');
    const isClickOnMenuBtn = event.target.closest('.mobile-menu-btn');

    if (!isClickInsideNav && !isClickOnMenuBtn && document.querySelector('.nav-menu').classList.contains('active')) {
      document.querySelector('.nav-menu').classList.remove('active');
    }
  });
</script>

</body>
</html>
