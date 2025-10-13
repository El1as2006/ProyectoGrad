<?php
session_start();
include_once '../conexion.php';

$message = '';
$error = '';
$validToken = false;
$tokenExpired = false;
$userId = null;

// Verificar si se proporciona el token
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = 'Token de recuperación no válido.';
} else {
    // Verificar token en base de datos
    $stmt = $conn->prepare('SELECT id_usuario, nombre, gmail_institucional, reset_token_expires FROM usuarios WHERE reset_token = ? AND activo = 1 LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            $userId = $user['id_usuario'];
            $userName = $user['nombre'];
            $userEmail = $user['gmail_institucional'];
            
            // Verificar si el token no ha expirado
            $currentTime = date('Y-m-d H:i:s');
            if ($user['reset_token_expires'] > $currentTime) {
                $validToken = true;
            } else {
                $tokenExpired = true;
                $error = 'El enlace de recuperación ha expirado. Por favor, solicita uno nuevo.';
            }
        } else {
            $error = 'Token de recuperación no válido o ya utilizado.';
        }
        $stmt->close();
    }
}

// Procesar el formulario de nueva contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    
    if (empty($newPassword) || empty($confirmPassword)) {
        $error = 'Por favor, complete todos los campos.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Actualizar contraseña
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare('UPDATE usuarios SET contrasena = ?, reset_token = NULL, reset_token_expires = NULL WHERE id_usuario = ?');
        
        if ($updateStmt) {
            $updateStmt->bind_param('si', $hashedPassword, $userId);
            
            if ($updateStmt->execute()) {
                $message = 'Tu contraseña ha sido actualizada exitosamente. Ya puedes iniciar sesión.';
                $validToken = false; // Deshabilitar el formulario
            } else {
                $error = 'Error al actualizar la contraseña. Por favor, inténtalo más tarde.';
            }
            $updateStmt->close();
        } else {
            $error = 'Error interno. Por favor, inténtalo más tarde.';
        }
    }
}

function isPasswordStrong($password) {
    return strlen($password) >= 6;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Sistema Biblioteca</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="../assets/css/material-icons-fix.css" rel="stylesheet" type="text/css" />    <style>
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
        .btn-reset {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            border-radius: 50px;
            width: 100%;
        }
        .btn-reset:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            color: white;
        }
        .btn-reset:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            opacity: 0.65;
        }
        .password-strength {
            margin-top: 10px;
        }
        .strength-meter {
            height: 4px;
            background-color: #e1e8ed;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        .strength-fill {
            height: 100%;
            transition: width 0.3s ease;
        }
        .strength-weak { background-color: #dc3545; width: 33%; }
        .strength-medium { background-color: #ffc107; width: 66%; }
        .strength-strong { background-color: #28a745; width: 100%; }
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
        .error-icon {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .text-center {
            text-align: center;
        }
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .reset-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .reset-header p {
            color: #6c757d;
            margin-bottom: 0;
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
            
            <?php if ($validToken): ?>
                <!-- Formulario de nueva contraseña -->
                <div class="reset-header">
                    <i class="fa fa-lock" style="font-size: 2rem; margin-bottom: 15px; color: #6c757d;"></i>
                    <h2>Nueva Contraseña</h2>
                    <p>Ingresa tu nueva contraseña segura</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="resetForm">
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               placeholder="Ingresa tu nueva contraseña" required minlength="6">
                        <div class="password-strength">
                            <div class="strength-meter">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <small class="form-text text-muted" id="strengthText">
                                La contraseña debe tener al menos 6 caracteres
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Confirma tu nueva contraseña" required>
                        <small class="form-text text-muted" id="matchText"></small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block btn-reset" id="submitBtn" disabled>
                        <i class="fa fa-check"></i> Actualizar Contraseña
                    </button>
                </form>
                
                <div class="back-to-login">
                    <a href="login.php" class="text-muted">
                        <i class="fa fa-arrow-left"></i> Volver al Login
                    </a>
                </div>
                
            <?php elseif ($message): ?>
                <!-- Éxito -->
                <div class="reset-header">
                    <i class="fa fa-check-circle success-icon"></i>
                    <h2>¡Contraseña Actualizada!</h2>
                    <p>Tu contraseña ha sido cambiada exitosamente</p>
                </div>
                
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                
                <a href="login.php" class="btn btn-primary btn-block btn-reset">
                    <i class="fa fa-sign-in"></i> Ir al Login
                </a>
                
            <?php else: ?>
                <!-- Error de token -->
                <div class="reset-header">
                    <i class="fa fa-exclamation-triangle error-icon"></i>
                    <h2>Enlace No Válido</h2>
                    <p><?php echo $tokenExpired ? 'El enlace ha expirado' : 'Token de recuperación inválido'; ?></p>
                </div>
                
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                
                <a href="forgot_password.php" class="btn btn-primary btn-block btn-reset">
                    <i class="fa fa-refresh"></i> Solicitar Nuevo Enlace
                </a>
                
                <div class="back-to-login">
                    <a href="login.php" class="text-muted">
                        <i class="fa fa-arrow-left"></i> Volver al Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const newPasswordInput = $('#new_password');
            const confirmPasswordInput = $('#confirm_password');
            const strengthFill = $('#strengthFill');
            const strengthText = $('#strengthText');
            const matchText = $('#matchText');
            const submitBtn = $('#submitBtn');

            function checkPasswordStrength(password) {
                let strength = 0;
                let feedback = [];

                if (password.length >= 6) strength += 1;
                if (password.length >= 8) strength += 1;
                if (/[a-z]/.test(password)) strength += 1;
                if (/[A-Z]/.test(password)) strength += 1;
                if (/[0-9]/.test(password)) strength += 1;
                if (/[^A-Za-z0-9]/.test(password)) strength += 1;

                return Math.min(strength, 3); // 0-3 scale
            }

            function updateStrengthMeter() {
                const password = newPasswordInput.val();
                const strength = checkPasswordStrength(password);
                
                strengthFill.removeClass('strength-weak strength-medium strength-strong');
                
                if (password.length === 0) {
                    strengthText.text('La contraseña debe tener al menos 6 caracteres').removeClass('text-success text-warning text-danger');
                } else if (strength <= 1) {
                    strengthFill.addClass('strength-weak');
                    strengthText.text('Contraseña débil').addClass('text-danger').removeClass('text-success text-warning');
                } else if (strength === 2) {
                    strengthFill.addClass('strength-medium');
                    strengthText.text('Contraseña media').addClass('text-warning').removeClass('text-success text-danger');
                } else {
                    strengthFill.addClass('strength-strong');
                    strengthText.text('Contraseña fuerte').addClass('text-success').removeClass('text-warning text-danger');
                }
            }

            function checkPasswordMatch() {
                const password = newPasswordInput.val();
                const confirmPassword = confirmPasswordInput.val();
                
                if (confirmPassword.length === 0) {
                    matchText.text('').removeClass('text-success text-danger');
                    return false;
                } else if (password === confirmPassword) {
                    matchText.text('Las contraseñas coinciden').addClass('text-success').removeClass('text-danger');
                    return true;
                } else {
                    matchText.text('Las contraseñas no coinciden').addClass('text-danger').removeClass('text-success');
                    return false;
                }
            }

            function updateSubmitButton() {
                const password = newPasswordInput.val();
                const confirmPassword = confirmPasswordInput.val();
                const isStrongEnough = password.length >= 6;
                const passwordsMatch = password === confirmPassword && confirmPassword.length > 0;
                
                if (isStrongEnough && passwordsMatch) {
                    submitBtn.prop('disabled', false);
                } else {
                    submitBtn.prop('disabled', true);
                }
            }

            newPasswordInput.on('input', function() {
                updateStrengthMeter();
                checkPasswordMatch();
                updateSubmitButton();
            });

            confirmPasswordInput.on('input', function() {
                checkPasswordMatch();
                updateSubmitButton();
            });

            // Prevenir submit si las contraseñas no coinciden
            $('#resetForm').on('submit', function(e) {
                const password = newPasswordInput.val();
                const confirmPassword = confirmPasswordInput.val();
                
                if (password !== confirmPassword || password.length < 6) {
                    e.preventDefault();
                    alert('Por favor, verifica que las contraseñas coincidan y tengan al menos 6 caracteres.');
                }
            });
        });
    </script>
</body>
</html>
