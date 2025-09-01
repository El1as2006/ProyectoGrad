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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            /* Updated background to gradient from yellow to black */
            background: linear-gradient(135deg, #FFD700 0%, #FFA000 25%, #333333 75%, #000000 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            box-shadow: 0 32px 64px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            /* Added subtle animation */
            animation: slideUp 0.6s ease-out;
            border: 1px solid rgba(255, 215, 0, 0.1);
        }

        /* Added slide up animation */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            padding: 2.5rem 2rem 1.5rem;
            text-align: center;
            /* Added subtle gradient background */
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.02) 0%, rgba(255, 215, 0, 0.05) 100%);
        }

        /* Added logo styling */
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.75rem;
            /* Added text shadow for better readability */
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); 
        }

        .card-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .card-content {
            padding: 0 2rem 2.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            /* Added letter spacing for better readability */
            letter-spacing: 0.025em;
        }

        .input-container {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 14px;
            width: 18px;
            height: 18px;
            color: #9ca3af;
            /* Added transition */
            transition: color 0.2s ease;
        }

        .input {
            width: 100%;
            padding: 14px 14px 14px 44px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white;
            /* Added subtle shadow */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .input:focus {
            outline: none;
            border-color: #FFD700;
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.1);
            /* Change icon color on focus */
        }

        .input:focus + .input-icon,
        .input:focus ~ .input-icon {
            color: #FFD700;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 14px;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 2px;
            width: 20px;
            height: 20px;
            border-radius: 4px;
            /* Added hover effect */
            transition: all 0.2s ease;
        }

        .password-toggle:hover {
            color: #FFD700;
            background: rgba(255, 215, 0, 0.1);
        }

        .forgot-link {
            color: #FFD700;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            /* Added transition */
            transition: all 0.2s ease;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: rgba(255, 215, 0, 0.8);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
            color: #1a1a1a;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1.25rem;
            /* Added text transform and shadow */
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
        }

        .btn:hover {
            background: linear-gradient(135deg, #FFC107 0%, #FFB300 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .text-center {
            text-align: center;
            margin-top: 2rem;
            /* Added padding and border */
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .register-link {
            color: #FFD700;
            text-decoration: none;
            font-weight: 600;
            /* Added transition */
            transition: all 0.2s ease;
        }

        .register-link:hover {
            text-decoration: underline;
            color: rgba(255, 215, 0, 0.8);
        }

        .text-gray {
            color: #666;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        /* Added responsive design */
        @media (max-width: 480px) {
            .card {
                margin: 0.5rem;
                border-radius: 12px;
            }
            
            .card-header {
                padding: 2rem 1.5rem 1rem;
            }
            
            .card-content {
                padding: 0 1.5rem 2rem;
            }
            
            .logo {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <!-- Added Santa Cecilia logo -->
            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Recurso%207-v9miyhZl7TVKTjoaZvWGS1aWqyWdk2.png" alt="Santa Cecilia Salesianos" class="logo">
            <h1 class="card-title">Iniciar Sesión</h1>
            <p class="card-description">Ingresa tus credenciales para acceder al sistema</p>
        </div>
        <div class="card-content">
            <form method="post" action="login.php" >
                <div class="form-group">
                    <label for="email" class="label">Correo Electrónico</label>
                    <div class="input-container">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input 
                            type="email" id="gmail_institucional" name="gmail_institucional" class="input" placeholder="correo@santacecilia.edu.sv" required value="<?php echo htmlspecialchars($_POST['gmail_institucional'] ?? ''); ?>">  
                        
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="label">Contraseña</label>
                    <div class="input-container">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 01-8 0v4h8z"/>
                        </svg>
                        <input 
                            type="password" id="contraseña" name="contraseña" class="input" placeholder="••••••••" required style="padding-right: 44px;"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <svg id="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
                    <a href="forgot-password.html" class="forgot-link">¿Olvidaste tu contraseña?</a>
                </div> -->

                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>

            <!-- <div class="text-center">
                <p class="text-gray">
                    ¿No tienes una cuenta? 
                    <a href="register.html" class="register-link">Regístrate aquí</a>
                </p>
            </div> -->
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('eye-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Here you would typically send the data to your PHP backend
            console.log('Login attempt:', { email, password });
            
            // Example: redirect to dashboard or show success message
            // window.location.href = 'dashboard.php';
            alert('Formulario enviado. Integra con tu backend PHP.');
        });
    </script>
</body>
</html>