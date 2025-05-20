<?php
session_start();

$host = 'localhost'; 
$dbname = 'biblioteca';
$username = 'root'; 
$password = 'Info2025/*-'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener lista de libros disponibles
$sqlLibros = "SELECT id, titulo, autor, genero, descripcion, archivo_pdf FROM libros WHERE disponible > 0";
if ($searchTerm) {
    $sqlLibros .= " AND (titulo LIKE :searchTerm OR autor LIKE :searchTerm OR genero LIKE :searchTerm)";
}
$stmtLibros = $pdo->prepare($sqlLibros);
if ($searchTerm) {
    $stmtLibros->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
}
$stmtLibros->execute();
$libros = $stmtLibros->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros Disponibles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="crm_body_bg">
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <section class="main_content dashboard_part">
        <div class="container-fluid g-0">
            <div class="row">
                <div class="col-lg-12 p-0">
                    <div class="header_iner d-flex justify-content-between align-items-center">
                        <div class="serach_field-area">
                            <div class="search_inner">
                                <form action="libros_disponibles.php" method="GET" id="searchForm">
                                    <div class="search_field">
                                        <input type="text" name="search" id="searchInput" placeholder="Buscar libro..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                                    </div>
                                    <button type="submit">
                                        <img src="./img/icon/icon_search.svg" alt>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <div class="main-title">
                    <h3 class="mb-0">Libros Disponibles</h3>
                </div>

                <div class="book-list" id="lista_libros">
                    <?php foreach ($libros as $libro): ?>
                        <div class="col">
                            <div class="card">
                                <img src="ver_portada.php?id=<?php echo $libro['id']; ?>" class="card-img-top" alt="Portada del libro">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($libro['titulo']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($libro['autor']); ?></h6>
                                    <p class="card-text"><?php echo htmlspecialchars($libro['descripcion']); ?></p>
                                    <a href="ver_pdf.php?id=<?php echo $libro['id']; ?>" class="btn btn-primary mt-2" target="_blank">Leer PDF</a>
                                    <button class="btn btn-secondary mt-2" onclick="imprimirPagina('ver_pdf.php?id=<?php echo $libro['id']; ?>')">Imprimir</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <script>
        function imprimirPagina(url) {
            var win = window.open(url, '_blank');
            win.focus();
            win.print();
        }

        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                $('.book-list .col').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
                });
            });
        });
    </script>

    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./js/custom.js"></script>
</body>
</html>