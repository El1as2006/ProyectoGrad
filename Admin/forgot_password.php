<?php
session_start();
include_once '../conexion.php';
include_once 'includes/email_service.php';

$message = '';
$error = '';
$step = 'request'; // request, sent

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Por favor, ingrese su correo electrónico.';
    } elseif (!isValidEmail($email)) {
        $error = 'Por favor, ingrese un correo electrónico válido.';
    } else {
        // Verificar si el email existe en la base de datos
        $stmt = $conn->prepare('SELECT id_usuario, nombre, gmail_institucional FROM usuarios WHERE gmail_institucional = ? AND activo = 1 LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($user = $result->fetch_assoc()) {
                // Generar token de recuperación
                $resetToken = generateSecureToken();
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora
                
                // Guardar token en base de datos
                $updateStmt = $conn->prepare('UPDATE usuarios SET reset_token = ?, reset_token_expires = ? WHERE id_usuario = ?');
                if ($updateStmt) {
                    $updateStmt->bind_param('ssi', $resetToken, $expiresAt, $user['id_usuario']);
                    
                    if ($updateStmt->execute()) {
                        // Enviar email
                        $emailService = new EmailService();
                        if ($emailService->sendPasswordResetEmail($user['gmail_institucional'], $user['nombre'], $resetToken)) {
                            $step = 'sent';
                            $message = 'Se ha enviado un enlace de recuperación a tu correo electrónico.';
                        } else {
                            $error = 'Error al enviar el correo. Por favor, inténtalo más tarde.';
                        }
                    } else {
                        $error = 'Error interno. Por favor, inténtalo más tarde.';
                    }
                    $updateStmt->close();
                } else {
                    $error = 'Error interno. Por favor, inténtalo más tarde.';
                }
            } else {
                // Por seguridad, mostramos el mismo mensaje aunque el email no exista
                $step = 'sent';
                $message = 'Si el correo está registrado, recibirás un enlace de recuperación.';
            }
            $stmt->close();
        } else {
            $error = 'Error interno. Por favor, inténtalo más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Sistema Biblioteca</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="../assets/css/material-icons-fix.css" rel="stylesheet" type="text/css" />
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
        .recovery-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .recovery-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .recovery-header p {
            color: #6c757d;
            margin-bottom: 0;
        }
        .recovery-icon {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-login a {
            color: #6c757d;
            text-decoration: none;
        }
        .back-to-login a:hover {
            text-decoration: underline;
        }
        .success-icon {
            font-size: 3rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            font-size: 14px;
        }
        .step.active {
            background: #007bff;
            color: white;
        }
        .step.inactive {
            background: #e9ecef;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="myform text-center">
            <div class="logo">
                <a href="index.php" class="logo-light">
                    <span class="logo-lg">
                        <img src="../assets/images/logo_chaleco.png" alt="logo">
                    </span>
                </a>
            </div>
            
            <?php if ($step === 'request'): ?>
                <!-- Paso 1: Solicitar recuperación -->
                <div class="recovery-header">
                    <i class="fa fa-key recovery-icon"></i>
                    <h2>Recuperar Contraseña</h2>
                    <p>Te ayudaremos a recuperar el acceso a tu cuenta</p>
                </div>
                
                <!-- Indicador de pasos -->
                <div class="step-indicator">
                    <div class="step active">1</div>
                    <div class="step inactive">2</div>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" id="recoveryForm">
                    <div class="form-group">
                        <label for="email">Correo institucional</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Ingresa tu correo institucional" required 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-paper-plane"></i> Enviar Enlace de Recuperación
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fa fa-info-circle"></i> 
                        Recibirás un enlace que expirará en 1 hora
                    </small>
                </div>
                
            <?php else: ?>
                <!-- Paso 2: Confirmación de envío -->
                <div class="recovery-header">
                    <i class="fa fa-check-circle success-icon"></i>
                    <h2>¡Correo Enviado!</h2>
                    <p>Revisa tu bandeja de entrada</p>
                </div>
                
                <!-- Indicador de pasos -->
                <div class="step-indicator">
                    <div class="step inactive">1</div>
                    <div class="step active">2</div>
                </div>
                
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($message) ?>
                </div>
                
                <p class="text-muted">
                    Revisa tu bandeja de entrada y también la carpeta de spam. 
                    El enlace expirará en 1 hora por seguridad.
                </p>
                
                <a href="forgot_password.php" class="btn btn-outline-secondary btn-block">
                    <i class="fa fa-refresh"></i> Enviar Otro Correo
                </a>
                
            <?php endif; ?>
            
            <div class="back-to-login">
                <a href="login.php" class="text-muted">
                    <i class="fa fa-arrow-left"></i> Volver al Login
                </a>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Validación del formulario
            $('#recoveryForm').on('submit', function(e) {
                const email = $('#email').val().trim();
                if (!email) {
                    e.preventDefault();
                    alert('Por favor, ingresa tu correo electrónico.');
                    return false;
                }
                
                // Validación básica de email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Por favor, ingresa un correo electrónico válido.');
                    return false;
                }
                
                // Mostrar loading
                $(this).find('button[type="submit"]').html('<i class="fa fa-spinner fa-spin"></i> Enviando...').prop('disabled', true);
            });
        });
    </script>
</body>
</html>
