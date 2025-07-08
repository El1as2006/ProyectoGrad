<?php
session_start();
include_once '../conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gmail_institucional = trim($_POST['gmail_institucional'] ?? '');
    $contraseña = trim($_POST['contraseña'] ?? '');

    if (empty($gmail_institucional) || empty($contraseña)) {
        $error = 'Por favor, ingrese su correo institucional y contraseña.';
    } else {
        // 1. Buscar en la tabla usuarios
        $stmt = $conn->prepare('SELECT id_usuario, nombre, contrasena, rol FROM usuarios WHERE gmail_institucional = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $gmail_institucional);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if (password_verify($contraseña, $row['contrasena'])) {
                    $_SESSION['user_id'] = $row['id_usuario'];
                    $_SESSION['user_name'] = $row['nombre'];
                    $_SESSION['user_rol'] = strtolower($row['rol']); // Asegura comparación consistente

                    // Redirige según el rol
                    switch ($_SESSION['user_rol']) {
                        case 'admin':
                            header('Location: index.php');
                            exit;
                        case 'docente':
                            header('Location: ../Docente/index.php');
                            exit;
                        case 'estudiante':
                            header('Location: ../index.php');
                            exit;
                        default:
                            $error = 'Rol de usuario no reconocido.';
                    }
                } else {
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                // 2. Buscar en la tabla estudiantes
                $stmt2 = $conn->prepare('SELECT id, nombre, contrasena FROM estudiantes WHERE gmail_institucional = ? LIMIT 1');
                if ($stmt2) {
                    $stmt2->bind_param('s', $gmail_institucional);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();

                    if ($estudiante = $result2->fetch_assoc()) {
                        if (password_verify($contraseña, $estudiante['contrasena'])) {
                            $_SESSION['user_id'] = $estudiante['id'];
                            $_SESSION['user_name'] = $estudiante['nombre'];
                            $_SESSION['user_rol'] = 'estudiante';

                            header('Location: ../index.php');
                            exit;
                        } else {
                            $error = 'Contraseña incorrecta.';
                        }
                    } else {
                        $error = 'Usuario no encontrado.';
                    }
                    $stmt2->close();
                } else {
                    $error = 'Error en la consulta de estudiantes: ' . $conn->error;
                }
            }
            $stmt->close();
        } else {
            $error = 'Error en la consulta de usuarios: ' . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
        }

        .myform {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            height: 80px;
            width: auto;
            margin-bottom: 20px;
        }

        .login-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-primary {
            border-radius: 50px;
        }

        .google.btn {
            background-color: #db4a39;
            color: white;
            border-radius: 50px;
            width: 100%;
        }

        .google.btn:hover {
            background-color: #c23321;
        }

        .login-or {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }

        .hr-or {
            height: 1px;
            background-color: #ccc;
        }

        .span-or {
            background: white;
            padding: 0 10px;
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="myform text-center">
            <div class="logo">
                <a href="index.html" class="logo-light">
                    <span class="logo-lg">
                        <img src="../assets/images/logo_chaleco.png" alt="logo">
                    </span>
                </a>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="" method="post" name="login" autocomplete="off">
                <div class="form-group">
                    <label for="gmail_institucional">Correo institucional</label>
                    <input type="email" name="gmail_institucional" class="form-control" id="gmail_institucional"
                        placeholder="Correo institucional" required autofocus
                        value="<?php echo htmlspecialchars($_POST['gmail_institucional'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" name="contraseña" class="form-control" id="contraseña"
                        placeholder="Contraseña" required>
                </div>
                <div class="form-group">
                    <p class="text-center mb-1">Al ingresar aceptas nuestros <a href="#">Términos de uso</a></p>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
                <div class="login-or">
                    <hr class="hr-or">
                </div>
                <div class="form-group text-center">
                    <a href="forgot_password.php" class="text-muted">
                        <i class="fa fa-lock"></i> ¿Olvidaste tu contraseña?
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>