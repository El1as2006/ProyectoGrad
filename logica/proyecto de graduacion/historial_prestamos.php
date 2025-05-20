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

$id_usuario = $_SESSION['id_usuario'];
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';

// Obtener lista de préstamos
if ($rol === 'estudiante') {
    $sqlPrestamos = "SELECT p.id_prestamo, p.fecha_prestamo, p.fecha_devolucion, p.status, l.titulo, l.autor
                     FROM prestamos p
                     JOIN libros l ON p.id_libro = l.id
                     WHERE p.id_usuario = :id_usuario";
    $stmtPrestamos = $pdo->prepare($sqlPrestamos);
    $stmtPrestamos->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
} else {
    $sqlPrestamos = "SELECT p.id_prestamo, p.fecha_prestamo, p.fecha_devolucion, p.status, l.titulo, l.autor, u.nombre AS estudiante
                     FROM prestamos p
                     JOIN libros l ON p.id_libro = l.id
                     JOIN usuarios u ON p.id_usuario = u.id_usuario";
    $stmtPrestamos = $pdo->prepare($sqlPrestamos);
}

$stmtPrestamos->execute();
$prestamos = $stmtPrestamos->fetchAll(PDO::FETCH_ASSOC);

// Manejar acciones de entrega y cancelación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['entregado'])) {
        $id_prestamo = $_POST['id_prestamo'];
        $fecha_devolucion = $_POST['fecha_devolucion'];
        $status = (new DateTime() > new DateTime($fecha_devolucion)) ? 'entregado con retraso' : 'entregado';

        $sqlUpdate = "UPDATE prestamos SET status = :status WHERE id_prestamo = :id_prestamo";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':status', $status, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
        $stmtUpdate->execute();

        header("Location: historial_prestamos.php");
        exit;
    }

    if (isset($_POST['cancelar'])) {
        $id_prestamo = $_POST['id_prestamo'];

        $sqlDelete = "DELETE FROM prestamos WHERE id_prestamo = :id_prestamo";
        $stmtDelete = $pdo->prepare($sqlDelete);
        $stmtDelete->bindParam(':id_prestamo', $id_prestamo, PDO::PARAM_INT);
        $stmtDelete->execute();

        header("Location: historial_prestamos.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Préstamos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                <form action="#">
                                    <div class="search_field">
                                        <input type="text" placeholder="Search here...">
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
                    <h3 class="mb-0">Historial de Préstamos</h3>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Título del Libro</th>
                            <th>Autor</th>
                            <?php if ($rol !== 'estudiante'): ?>
                                <th>Estudiante</th>
                            <?php endif; ?>
                            <th>Fecha de Préstamo</th>
                            <th>Fecha de Devolución</th>
                            <th>Estado</th>
                            <?php if ($rol !== 'estudiante'): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr class="<?php echo $prestamo['status'] == 'entregado con retraso' ? 'table-warning' : ''; ?>">
                                <td><?php echo htmlspecialchars($prestamo['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['autor']); ?></td>
                                <?php if ($rol !== 'estudiante'): ?>
                                    <td><?php echo htmlspecialchars($prestamo['estudiante']); ?></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($prestamo['fecha_prestamo']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['fecha_devolucion']); ?></td>
                                <td><?php echo htmlspecialchars($prestamo['status']); ?></td>
                                <?php if ($rol !== 'estudiante'): ?>
                                    <td>
                                        <?php if ($prestamo['status'] == 'no entregado'): ?>
                                            <form action="historial_prestamos.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="id_prestamo" value="<?php echo $prestamo['id_prestamo']; ?>">
                                                <input type="hidden" name="fecha_devolucion" value="<?php echo $prestamo['fecha_devolucion']; ?>">
                                                <button type="submit" name="entregado" class="btn btn-success btn-sm">Entregado</button>
                                            </form>
                                            <form action="historial_prestamos.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="id_prestamo" value="<?php echo $prestamo['id_prestamo']; ?>">
                                                <button type="submit" name="cancelar" class="btn btn-danger btn-sm">Cancelar</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge badge-success"><?php echo htmlspecialchars($prestamo['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./js/custom.js"></script>
</body>
</html>