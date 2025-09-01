<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = include_once 'conexion.php';

$termino = $_GET['q'] ?? '';
$resultados = [];

if (!empty($termino)) {
    $termino_escapado = $conn->real_escape_string($termino);

    $sql = "
        SELECT id, titulo, autor, anio_publicacion, isbn, descripcion, disponible, tipo_libro
        FROM libros
        WHERE 
            titulo LIKE '%$termino_escapado%' OR 
            autor LIKE '%$termino_escapado%' OR 
            isbn LIKE '%$termino_escapado%'
        LIMIT 50
    ";

    $res = $conn->query($sql);

    if ($res) {
        while ($fila = $res->fetch_assoc()) {
            $resultados[] = $fila;
        }
    } else {
        error_log("Error en la búsqueda: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda</title>
    <link rel="stylesheet" href="/ProyectoGrad-Logica-ProyectoGrad/assets/css/indexstyle.css">
    <link rel="stylesheet" href="/ProyectoGrad-Logica-ProyectoGrad/assets/css/librodetalles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .resultados-busqueda {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 2rem;
        }

        .libro-card {
            flex: 1 1 calc(33.333% - 2rem);
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .libro-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .libro-card-content {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .libro-card-content h3 {
            margin: 0 0 0.5rem;
            font-size: 1.2rem;
            color: #333;
        }

        .libro-card-content p {
            margin: 0.3rem 0;
            color: #555;
        }

        .libro-card-content .btn-ver {
            margin-top: auto;
            padding: 0.5rem;
            background-color: #0077cc;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .libro-card-content .btn-ver:hover {
            background-color: #005fa3;
        }

        @media (max-width: 768px) {
            .libro-card {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>

<header>
    <?php include 'header.php'; ?>
</header>

<main class="container">
    <h2>Resultados para: "<?php echo htmlspecialchars($termino); ?>"</h2>

    <?php if (count($resultados) > 0): ?>
        <div class="resultados-busqueda">
            <?php foreach ($resultados as $libro): ?>
                <div class="libro-card">
                    <img src="/ProyectoGrad-Logica-ProyectoGrad/assets/book/img_libros.jpg" alt="Portada del libro">

                    <div class="libro-card-content">
                        <h3><?php echo htmlspecialchars($libro['titulo']); ?></h3>
                        <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['autor']); ?></p>
                        <p><strong>Año:</strong> <?php echo $libro['anio_publicacion']; ?></p>
                        <p><strong>Tipo:</strong> <?php echo ucfirst($libro['tipo_libro']); ?></p>
                        <p><strong>Disponible:</strong> <?php echo $libro['disponible'] ? 'Sí' : 'No'; ?></p>
                        <p><?php echo mb_strimwidth(htmlspecialchars($libro['descripcion']), 0, 100, '...'); ?></p>

                        <a href="libro_detalle.php?id=<?php echo $libro['id']; ?>" class="btn-ver">📖 Ver Detalles</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No se encontraron resultados para tu búsqueda.</p>
    <?php endif; ?>
</main>

</body>
</html>
