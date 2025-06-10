<?php
session_start();
include_once '../conexion.php'; // Assuming conexion.php is in your_project_root/

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gmail_institucional = trim($_POST['gmail_institucional'] ?? '');
    $contraseña = trim($_POST['contraseña'] ?? '');

    if (empty($gmail_institucional) || empty($contraseña)) {
        $error = 'Por favor, ingrese su correo institucional y contraseña.';
    } else {
        $stmt = $conn->prepare('SELECT id_usuario, nombre, contrasena, rol FROM usuarios WHERE gmail_institucional = ? LIMIT 1');
        if (!$stmt) {
            $error = 'Error en la consulta: ' . $conn->error;
        } else {
            $stmt->bind_param('s', $gmail_institucional);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if (password_verify($contraseña, $row['contrasena'])) {
                    $_SESSION['user_id'] = $row['id_usuario'];
                    $_SESSION['user_name'] = $row['nombre'];
                    $_SESSION['user_rol'] = $row['rol'];

                    // You can uncomment this line temporarily for debugging:
                    // echo "DEBUG: User Role from DB: '" . htmlspecialchars($row['rol']) . "'<br>";

                    if ($_SESSION['user_rol'] === 'admin') {
                        // Admin index is in the same folder as login.php (Admin folder)
                        header('Location: index.php'); // This looks correct for admin
                        exit;
                    } elseif ($_SESSION['user_rol'] === 'estudiante') {
                        // Student index is one level UP from the Admin folder
                        header('Location: ../index.php'); // Corrected path for student
                        exit;
                    } else {
                        $error = 'Rol de usuario no reconocido. Por favor, contacte al soporte.';
                    }
                } else {
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                $error = 'Usuario no encontrado.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
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
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
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
            text-align: center;
            width: 100%;
        }
        .google.btn:hover {
            background-color: #c23321;
            color: white;
        }
        .login-or {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }
        .hr-or {
            height: 1px;
            margin: 0;
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
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="" method="post" name="login" autocomplete="off">
                <div class="form-group">
                    <label for="gmail_institucional">Correo institucional</label>
                    <input type="email" name="gmail_institucional" class="form-control" id="gmail_institucional" placeholder="Correo institucional" required autofocus value="<?php echo htmlspecialchars($_POST['gmail_institucional'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" name="contraseña" class="form-control" id="contraseña" placeholder="Contraseña" required>
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