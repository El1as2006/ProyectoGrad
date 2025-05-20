<!DOCTYPE html>
<html lang="en">
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
            height: 100%;
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
<?php
require "../conexion.php";

if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gmail_institucional = trim($_POST['email']);
    $contraseña = trim($_POST['pass']);

    if (empty($gmail_institucional) || empty($contraseña)) {
        $error = "Todos los campos son obligatorios y no deben contener solo espacios en blanco.";
    } else {
        $sql = "SELECT id_user, pass, user_type FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $gmail_institucional, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                if (password_verify($contraseña, $usuario['pass'])) {
                    $_SESSION['id_user'] = $usuario['id_user'];
                    $_SESSION['email'] = $gmail_institucional;
                    $_SESSION['user_type'] = $usuario['email'];
                    header("Location: admin.php");
                    exit;
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "Correo institucional no encontrado.";
            }
        } catch (PDOException $e) {
            $error = "Error en la consulta: " . $e->getMessage();
        }
    }
}
?>
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
            <form action="" method="post" name="login">
                <div class="form-group">
                    <label for="email">Correo Institucional</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="pass" class="form-control" id="pass" placeholder="Enter Password">
                </div>
                <!-- <div class="form-group">
                    <p class="text-center mb-1">By signing up you accept our <a href="#">Terms Of Use</a></p>
                </div> -->
                <button type="submit" class="btn btn-primary btn-block">Login</button>

                <!-- <div class="login-or">
                    <hr class="hr-or">
                    <span class="span-or">or</span>
                </div>

                <div class="form-group">
                    <a href="javascript:void();" class="google btn"><i class="fa fa-google-plus"></i> Signup using Google</a>
                </div> -->
                <div class="form-group">
                    <p class="text-center mb-0">Don't have an account? <a href="register.php">Sign up here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>