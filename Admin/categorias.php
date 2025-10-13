<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$mensaje = '';
$errores = [];
$nombre = $descripcion = $color = $icono = '';
$categoria_id = 0;
$modo = 'agregar'; // agregar, editar

// Procesar parámetros GET para edición
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $categoria_id = (int)$_GET['editar'];
    $modo = 'editar';
    
    $stmt = $conn->prepare('SELECT nombre, descripcion, color, icono FROM categorias_libros WHERE id = ?');
    $stmt->bind_param('i', $categoria_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($categoria = $result->fetch_assoc()) {
        $nombre = $categoria['nombre'];
        $descripcion = $categoria['descripcion'];
        $color = $categoria['color'];
        $icono = $categoria['icono'];
    } else {
        $errores[] = 'Categoría no encontrada.';
        $modo = 'agregar';
    }
    $stmt->close();
}

// Procesar eliminación
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $categoria_id_eliminar = (int)$_GET['eliminar'];
    
    // Verificar si hay libros asociados
    $stmt = $conn->prepare('SELECT COUNT(*) as total FROM libros WHERE categoria_id = ?');
    $stmt->bind_param('i', $categoria_id_eliminar);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_libros = $result->fetch_assoc()['total'];
    $stmt->close();
    
    if ($total_libros > 0) {
        $errores[] = "No se puede eliminar la categoría porque tiene $total_libros libros asociados.";
    } else {
        $stmt = $conn->prepare('DELETE FROM categorias_libros WHERE id = ?');
        $stmt->bind_param('i', $categoria_id_eliminar);
        if ($stmt->execute()) {
            $mensaje = 'Categoría eliminada correctamente.';
        } else {
            $errores[] = 'Error al eliminar la categoría.';
        }
        $stmt->close();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $color = trim($_POST['color'] ?? '#007bff');
    $icono = trim($_POST['icono'] ?? 'mdi-book');
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $modo = $_POST['modo'] ?? 'agregar';
    
    // Validaciones
    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        $errores[] = 'Color inválido.';
    }
    
    if (empty($errores)) {
        if ($modo === 'editar' && $categoria_id > 0) {
            // Actualizar categoría existente
            $stmt = $conn->prepare('UPDATE categorias_libros SET nombre = ?, descripcion = ?, color = ?, icono = ? WHERE id = ?');
            $stmt->bind_param('ssssi', $nombre, $descripcion, $color, $icono, $categoria_id);
            if ($stmt->execute()) {
                $mensaje = 'Categoría actualizada correctamente.';
            } else {
                $errores[] = 'Error al actualizar la categoría.';
            }
            $stmt->close();
        } else {
            // Agregar nueva categoría
            $stmt = $conn->prepare('INSERT INTO categorias_libros (nombre, descripcion, color, icono) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $nombre, $descripcion, $color, $icono);
            if ($stmt->execute()) {
                $mensaje = 'Categoría agregada correctamente.';
                $nombre = $descripcion = '';
                $color = '#007bff';
                $icono = 'mdi-book';
            } else {
                $errores[] = 'Error al guardar la categoría.';
            }
            $stmt->close();
        }
    }
}

// Obtener lista de categorías
$categorias = [];
$result = $conn->query('SELECT id, nombre, descripcion, color, icono, activo, 
                               (SELECT COUNT(*) FROM libros WHERE categoria_id = categorias_libros.id) as total_libros
                        FROM categorias_libros ORDER BY nombre');
if ($result) {
    $categorias = $result->fetch_all(MYSQLI_ASSOC);
}

// Lista de iconos disponibles
$iconos_disponibles = [
    'mdi-book' => 'Libro',
    'mdi-book-open-variant' => 'Libro Abierto',
    'mdi-flask' => 'Ciencia',
    'mdi-history' => 'Historia',
    'mdi-lightbulb-on' => 'Ideas/Filosofía',
    'mdi-account-group' => 'Ciencias Sociales',
    'mdi-palette' => 'Arte',
    'mdi-account-circle' => 'Biografías',
    'mdi-school' => 'Educación',
    'mdi-brain' => 'Psicología',
    'mdi-chart-line' => 'Economía',
    'mdi-gavel' => 'Derecho',
    'mdi-medical-bag' => 'Medicina',
    'mdi-tools' => 'Ingeniería',
    'mdi-calculator' => 'Matemáticas',
    'mdi-atom' => 'Física',
    'mdi-test-tube' => 'Química',
    'mdi-leaf' => 'Biología',
    'mdi-laptop' => 'Informática',
    'mdi-translate' => 'Idiomas',
    'mdi-church' => 'Religión',
    'mdi-soccer' => 'Deportes',
    'mdi-chef-hat' => 'Cocina',
    'mdi-map' => 'Viajes',
    'mdi-human-handsup' => 'Autoayuda',
    'mdi-teddy-bear' => 'Infantil',
    'mdi-feather' => 'Poesía',
    'mdi-drama-masks' => 'Teatro',
    'mdi-fountain-pen-tip' => 'Ensayo',
    'mdi-library' => 'Referencia'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>Gestión de Categorías | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <link href="../assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/material-icons-fix.css" rel="stylesheet" type="text/css" /><style>
        .categoria-preview {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            color: white;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .categoria-preview i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }
        .icono-option {
            cursor: pointer;
            padding: 0.75rem;
            margin: 0.25rem;
            border: 2px solid transparent;
            border-radius: 0.25rem;
            display: inline-block;
            transition: all 0.2s ease;
            text-align: center;
            min-width: 40px;
            min-height: 40px;
        }
        .icono-option:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            transform: scale(1.1);
        }
        .icono-option.selected {
            border-color: #007bff;
            background-color: #e3f2fd;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }
        .icono-option i {
            font-size: 1.25rem;
            color: #495057;
        }
        .icono-option.selected i {
            color: #007bff;
        }
        .iconos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 5px;
            max-height: 200px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'includes/session_check.php'; ?>
        <?php include 'includes/sidebar.php'; ?>
        <div class="content-page">
            <div class="content">
                <div class="container-fluid pt-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Gestión de Categorías</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Formulario -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><?= $modo === 'editar' ? 'Editar' : 'Agregar' ?> Categoría</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-success"><?= $mensaje ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($errores)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php foreach ($errores as $error): ?>
                                                    <li><?= htmlspecialchars($error) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <form method="post">
                                        <input type="hidden" name="categoria_id" value="<?= $categoria_id ?>">
                                        <input type="hidden" name="modo" value="<?= $modo ?>">
                                        
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                                   value="<?= htmlspecialchars($nombre) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($descripcion) ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="color" class="form-label">Color</label>
                                            <input type="color" class="form-control form-control-color" id="color" name="color" 
                                                   value="<?= htmlspecialchars($color) ?>" onchange="updatePreview()">
                                        </div>                                        <div class="mb-3">
                                            <label class="form-label">Icono</label>
                                            <input type="hidden" id="icono" name="icono" value="<?= htmlspecialchars($icono) ?>">
                                            <div class="iconos-grid">
                                                <?php foreach ($iconos_disponibles as $icono_clase => $icono_nombre): ?>
                                                    <span class="icono-option" data-icono="<?= $icono_clase ?>" 
                                                          onclick="selectIcon('<?= $icono_clase ?>')" 
                                                          title="<?= $icono_nombre ?>">
                                                        <i class="<?= $icono_clase ?>"></i>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Vista Previa</label>
                                            <div id="categoria-preview" class="categoria-preview" style="background-color: <?= $color ?>;">
                                                <i class="<?= $icono ?>"></i>
                                                <span id="preview-nombre"><?= htmlspecialchars($nombre ?: 'Nombre de Categoría') ?></span>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <?= $modo === 'editar' ? 'Actualizar' : 'Agregar' ?> Categoría
                                            </button>
                                            <?php if ($modo === 'editar'): ?>
                                                <a href="categorias.php" class="btn btn-secondary">Cancelar</a>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Categorías -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Categorías Existentes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Categoría</th>
                                                    <th>Descripción</th>
                                                    <th>Libros</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($categorias as $cat): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="categoria-preview me-2" style="background-color: <?= $cat['color'] ?>; padding: 0.25rem 0.5rem;">
                                                                    <i class="<?= $cat['icono'] ?>"></i>
                                                                    <?= htmlspecialchars($cat['nombre']) ?>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td><?= htmlspecialchars($cat['descripcion']) ?></td>
                                                        <td>
                                                            <span class="badge bg-info"><?= $cat['total_libros'] ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?= $cat['activo'] ? 'bg-success' : 'bg-secondary' ?>">
                                                                <?= $cat['activo'] ? 'Activo' : 'Inactivo' ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="?editar=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </a>
                                                            <?php if ($cat['total_libros'] == 0): ?>
                                                                <a href="?eliminar=<?= $cat['id'] ?>" 
                                                                   class="btn btn-sm btn-outline-danger"
                                                                   onclick="return confirm('¿Está seguro de que desea eliminar esta categoría?')">
                                                                    <i class="mdi mdi-delete"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
    <script src="includes/notifications.js"></script>
    
    <script>
        function selectIcon(iconClass) {
            // Remover selección anterior
            document.querySelectorAll('.icono-option').forEach(el => el.classList.remove('selected'));
            
            // Seleccionar nuevo icono
            document.querySelector(`[data-icono="${iconClass}"]`).classList.add('selected');
            document.getElementById('icono').value = iconClass;
            
            updatePreview();
        }

        function updatePreview() {
            const nombre = document.getElementById('nombre').value || 'Nombre de Categoría';
            const color = document.getElementById('color').value;
            const icono = document.getElementById('icono').value;
            
            const preview = document.getElementById('categoria-preview');
            preview.style.backgroundColor = color;
            preview.innerHTML = `<i class="${icono}"></i><span id="preview-nombre">${nombre}</span>`;
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar icono actual
            const iconoActual = '<?= $icono ?>';
            if (iconoActual) {
                selectIcon(iconoActual);
            }
            
            // Actualizar preview cuando cambie el nombre
            document.getElementById('nombre').addEventListener('input', updatePreview);
        });
    </script>
</body>
</html>
