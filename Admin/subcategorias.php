<?php
// Conexión PDO recomendada
try {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    include '../conexion.php';


} catch (PDOException $e) {
    echo "Error en la inserción: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Gestión de Subcategorías | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/css/vendor.min.css" rel="stylesheet" />
    <link href="../assets/css/app-saas.min.css" rel="stylesheet" id="app-style" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" />
    <style>
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
        }

        .iconos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 5px;
            max-height: 200px;
            overflow-y: auto;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 6px;
        }

        .icono-option {
            padding: 0.5rem;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .icono-option:hover {
            background: #e9ecef;
        }

        .icono-option.selected {
            border-color: #007bff;
            background: #dbeeff;
        }
    </style>
    <script>
        function selectIcon(icon) {
            document.getElementById('icono').value = icon;
            document.querySelectorAll('.icono-option').forEach(el => el.classList.remove('selected'));
            document.querySelector(`[data-icono="${icon}"]`).classList.add('selected');
            document.querySelector('#preview-icon').className = icon;
        }
        function updatePreview() {
            document.getElementById('categoria-preview').style.backgroundColor = document.getElementById('color').value;
            document.getElementById('preview-nombre').innerText = document.getElementById('nombre').value;
        }
    </script>
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
                            <h4 class="page-title">Gestión de Subcategorías</h4>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Formulario -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><?= $modo === 'editar' ? 'Editar' : 'Agregar' ?> Subcategoría</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($mensaje): ?>
                                        <div class="alert alert-success"><?= $mensaje ?></div><?php endif; ?>
                                    <?php if (!empty($errores)): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0"><?php foreach ($errores as $error): ?>
                                                    <li><?= $error ?></li><?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post">
                                        <input type="hidden" name="modo" value="<?= $modo ?>">
                                        <input type="hidden" name="subcategoria_id" value="<?= $subcategoria_id ?>">

                                        <div class="mb-3">
                                            <label class="form-label">Categoría *</label>
                                            <select name="categoria_id" class="form-select" required>
                                                <option value="">Seleccione</option>
                                                <?php foreach ($categorias as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $categoria_id) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cat['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Nombre *</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                value="<?= htmlspecialchars($nombre) ?>" oninput="updatePreview()"
                                                required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea class="form-control"
                                                name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Color</label>
                                            <input type="color" class="form-control form-control-color" id="color"
                                                name="color" value="<?= $color ?: '#007bff' ?>"
                                                onchange="updatePreview()">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Icono</label>
                                            <input type="hidden" id="icono" name="icono" value="<?= $icono ?>">
                                            <div class="iconos-grid">
                                                <?php foreach ($iconos_disponibles as $clase => $nombre_icono): ?>
                                                    <div class="icono-option <?= ($clase == $icono) ? 'selected' : '' ?>"
                                                        data-icono="<?= $clase ?>" onclick="selectIcon('<?= $clase ?>')">
                                                        <i class="<?= $clase ?>"></i>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Vista Previa</label>
                                            <div id="categoria-preview" class="categoria-preview"
                                                style="background-color: <?= $color ?>;">
                                                <i id="preview-icon" class="<?= $icono ?>"></i>
                                                <span
                                                    id="preview-nombre"><?= $nombre ?: 'Nombre de Subcategoría' ?></span>
                                            </div>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="activo" id="activo"
                                                <?= $activo ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="activo">Activo</label>
                                        </div>

                                        <button class="btn btn-primary w-100" type="submit">
                                            <?= $modo === 'editar' ? 'Actualizar' : 'Agregar' ?> Subcategoría
                                        </button>
                                        <?php if ($modo === 'editar'): ?>
                                            <a href="subcategorias.php" class="btn btn-secondary mt-2 w-100">Cancelar</a>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Lista -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Subcategorías Existentes</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Categoría</th>
                                                <th>Subcategoría</th>
                                                <th>Descripción</th>
                                                <th>Libros</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($subcategorias as $sub): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($sub['categoria_nombre']) ?></td>
                                                    <td>
                                                        <div class="categoria-preview"
                                                            style="background-color: <?= $sub['color'] ?>;">
                                                            <i class="<?= $sub['icono'] ?>"></i>
                                                            <?= htmlspecialchars($sub['nombre']) ?>
                                                        </div>
                                                    </td>
                                                    <td><?= htmlspecialchars($sub['descripcion']) ?></td>
                                                    <td><span class="badge bg-info"><?= $sub['total_libros'] ?></span></td>
                                                    <td>
                                                        <span
                                                            class="badge <?= $sub['activo'] ? 'bg-success' : 'bg-secondary' ?>">
                                                            <?= $sub['activo'] ? 'Activo' : 'Inactivo' ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="?editar=<?= $sub['id'] ?>"
                                                            class="btn btn-sm btn-outline-primary"><i
                                                                class="mdi mdi-pencil"></i></a>
                                                        <?php if ($sub['total_libros'] == 0): ?>
                                                            <a href="?eliminar=<?= $sub['id'] ?>"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('¿Eliminar esta subcategoría?')"><i
                                                                    class="mdi mdi-delete"></i></a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($subcategorias)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No hay subcategorías registradas.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!-- row -->
                </div>
            </div>
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/vendor.min.js"></script>
    <script src="../assets/js/app.min.js"></script>
</body>

</html>