<?php
include_once '../conexion.php';

$busqueda = trim($_GET['q'] ?? '');
$id_usuario = $_SESSION['user_id'] ?? '';

$sql = 'SELECT id, titulo, autor, genero, tipo_libro, año_publicacion, isbn, descripcion, disponible, archivo_pdf FROM libros WHERE id_usuario = ?';
$params = [$id_usuario];
$types = 'i';
if ($busqueda !== '') {
    $sql .= ' AND (titulo LIKE ? OR autor LIKE ? OR genero LIKE ? OR tipo_libro LIKE ? OR año_publicacion LIKE ? OR isbn LIKE ? OR descripcion LIKE ?)';
    $like = "%$busqueda%";
    $params = [$id_usuario, $like, $like, $like, $like, $like, $like, $like];
    $types = 'isssssss';
}
$sql .= ' ORDER BY id DESC';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Buscar Libros | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="wrapper">
        <?php include_once 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card mt-4">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Buscar Libros</h4>
                                    <a href="add_book.php" class="btn btn-light btn-sm">+ Añadir Libro</a>
                                </div>
                                <div class="card-body">
                                    <form class="mb-3" method="get">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="q" placeholder="Buscar por título, autor, género, tipo, año, ISBN o descripción" value="<?= htmlspecialchars($busqueda) ?>">
                                            <button class="btn btn-info" type="submit">Buscar</button>
                                            <a href="buscar_libros.php" class="btn btn-outline-secondary">Limpiar</a>
                                        </div>
                                    </form>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Título</th>
                                                    <th>Autor</th>
                                                    <th>Género</th>
                                                    <th>Tipo</th>
                                                    <th>Año</th>
                                                    <th>ISBN</th>
                                                    <th>Descripción</th>
                                                    <th>PDF</th>
                                                    <th>Disponible</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                                                    <td><?= htmlspecialchars($row['autor']) ?></td>
                                                    <td><?= htmlspecialchars($row['genero']) ?></td>
                                                    <td><?= htmlspecialchars($row['tipo_libro']) ?></td>
                                                    <td><?= htmlspecialchars($row['año_publicacion']) ?></td>
                                                    <td><?= htmlspecialchars($row['isbn']) ?></td>
                                                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                                                    <td>
                                                        <?php if ($row['archivo_pdf']): ?>
                                                            <a href="../<?= htmlspecialchars($row['archivo_pdf']) ?>" target="_blank">Ver PDF</a>
                                                        <?php else: ?>
                                                            <span class="text-muted">No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $row['disponible'] ? 'Sí' : 'No' ?></td>
                                                    <td>
                                                        <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                                        <a href="delete_book.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este libro?')">Eliminar</a>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>
</html>
