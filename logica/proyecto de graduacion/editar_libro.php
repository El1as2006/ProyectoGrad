<?php
session_start();

$host = 'localhost'; 
$dbname = 'biblioteca';
$username = 'root'; 
$password = 'Info2025/*-'; 

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id_libro = $_GET['id'];
} else {
    echo "ID no recibido en la URL.<br>";
    exit();
}

if (!is_numeric($id_libro)) {
    echo "ID no válido o no numérico.<br>";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->bind_param("i", $id_libro);
$stmt->execute();
$result = $stmt->get_result();
$libro = $result->fetch_assoc();

if (!$libro) {
    echo "Libro no encontrado.<br>";
    exit();
}

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y validar los datos del formulario
    $titulo = htmlspecialchars(trim($_POST['titulo']));
    $autor = htmlspecialchars(trim($_POST['autor']));
    $genero = htmlspecialchars(trim($_POST['genero']));
    $año_publicacion = $_POST['año_publicacion'];
    $isbn = $_POST['isbn'];
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    // Actualizar el libro en la base de datos
    $stmt = $conn->prepare("UPDATE libros SET titulo = ?, autor = ?, genero = ?, año_publicacion = ?, isbn = ?, descripcion = ?, disponible = ? WHERE id = ?");
    $stmt->bind_param("ssssssii", $titulo, $autor, $genero, $año_publicacion, $isbn, $descripcion, $disponible, $id_libro);

    if ($stmt->execute()) {
        echo "Libro actualizado correctamente.<br>";
    } else {
        echo "Error al actualizar el libro: " . $stmt->error . "<br>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="./img/logo_chaleco.png" type="image/png">
</head>
<body class="crm_body_bg">
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <section class="main_content dashboard_part">
        <div class="container mt-5">
            <div class="main-title">
                <h3 class="mb-0">Editar Libro</h3>
            </div>

            <form action="editar_libro.php?id=<?php echo $id_libro; ?>" method="POST">
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($libro['titulo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="autor">Autor</label>
                    <input type="text" class="form-control" id="autor" name="autor" value="<?php echo htmlspecialchars($libro['autor']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="genero">Género</label>
                    <input type="text" class="form-control" id="genero" name="genero" value="<?php echo htmlspecialchars($libro['genero']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="año_publicacion">Año de Publicación</label>
                    <input type="number" class="form-control" id="año_publicacion" name="año_publicacion" value="<?php echo htmlspecialchars($libro['año_publicacion']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($libro['isbn']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($libro['descripcion']); ?></textarea>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="disponible" name="disponible" <?php echo $libro['disponible'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="disponible">Disponible</label>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Libro</button>
            </form>
        </div>
    </section>

    <script src="./js/jquery1-3.4.1.min.js"></script>
    <script src="./js/popper1.min.js"></script>
    <script src="./js/bootstrap1.min.js"></script>
    <script src="./js/metisMenu.js"></script>
    <script src="./js/custom.js"></script>
</body>
</html>