<?php
session_start();
include_once '../conexion.php';

// Script de pruebas para el sistema de recuperación de contraseña
// Este archivo ayuda a verificar que todos los componentes funcionen correctamente

echo "<h2>🔧 Pruebas del Sistema de Recuperación de Contraseña</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
    .success { color: #28a745; }
    .error { color: #dc3545; }
    .warning { color: #ffc107; }
    .info { color: #17a2b8; }
    .code { background: #e9ecef; padding: 10px; border-radius: 3px; font-family: monospace; }
</style>";

// Función para mostrar resultados de pruebas
function showTestResult($testName, $result, $message = '') {
    $icon = $result ? "✅" : "❌";
    $class = $result ? 'success' : 'error';
    echo "<p><span class='$class'>$icon $testName</span>";
    if ($message) echo " - $message";
    echo "</p>";
    return $result;
}

// 1. Verificar conexión a base de datos
echo "<div class='test-section'>";
echo "<h3>1. Verificación de Base de Datos</h3>";

$dbConnected = false;
if ($conn && $conn->ping()) {
    $dbConnected = true;
    showTestResult("Conexión a base de datos", true);
} else {
    showTestResult("Conexión a base de datos", false, "Error: " . ($conn ? $conn->error : "Conexión no establecida"));
}

// Verificar tabla usuarios y campos necesarios
if ($dbConnected) {
    $result = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'reset_token'");
    showTestResult("Columna 'reset_token' existe", $result && $result->num_rows > 0);
    
    $result = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'reset_token_expires'");
    showTestResult("Columna 'reset_token_expires' existe", $result && $result->num_rows > 0);
    
    // Verificar que hay usuarios de prueba
    $result = $conn->query("SELECT COUNT(*) as count FROM usuarios WHERE activo = 1");
    if ($result) {
        $row = $result->fetch_assoc();
        showTestResult("Usuarios activos en sistema", $row['count'] > 0, "Total: " . $row['count']);
    }
}
echo "</div>";

// 2. Verificar archivos del sistema
echo "<div class='test-section'>";
echo "<h3>2. Verificación de Archivos</h3>";

$files = [
    'forgot_password.php' => 'Página de solicitud de recuperación',
    'reset_password.php' => 'Página de cambio de contraseña',
    'includes/email_service.php' => 'Servicio de email',
    'includes/email_config.php' => 'Configuración de email',
];

foreach ($files as $file => $description) {
    $exists = file_exists($file);
    showTestResult($description, $exists, $file);
}
echo "</div>";

// 3. Verificar PHPMailer
echo "<div class='test-section'>";
echo "<h3>3. Verificación de PHPMailer</h3>";

$phpmailerFiles = [
    '../PHPMailer-master/src/PHPMailer.php',
    '../PHPMailer-master/src/SMTP.php',
    '../PHPMailer-master/src/Exception.php',
];

$phpmailerOk = true;
foreach ($phpmailerFiles as $file) {
    $exists = file_exists($file);
    showTestResult(basename($file), $exists);
    if (!$exists) $phpmailerOk = false;
}

if ($phpmailerOk) {
    try {
        require_once '../PHPMailer-master/src/PHPMailer.php';
        require_once '../PHPMailer-master/src/SMTP.php';
        require_once '../PHPMailer-master/src/Exception.php';
        showTestResult("Clases PHPMailer cargadas", true);
    } catch (Exception $e) {
        showTestResult("Clases PHPMailer cargadas", false, $e->getMessage());
    }
}
echo "</div>";

// 4. Verificar configuración de email
echo "<div class='test-section'>";
echo "<h3>4. Verificación de Configuración de Email</h3>";

if (file_exists('includes/email_config.php')) {
    $config = include 'includes/email_config.php';
    
    showTestResult("Archivo de configuración cargado", true);
    showTestResult("Host SMTP configurado", !empty($config['smtp']['host']));
    showTestResult("Puerto SMTP configurado", !empty($config['smtp']['port']));
    showTestResult("Usuario SMTP configurado", !empty($config['smtp']['username']));
    
    // Verificar si las credenciales son las de ejemplo (necesitan ser cambiadas)
    $needsConfig = ($config['smtp']['username'] === 'tu_email@gmail.com' || 
                   $config['smtp']['password'] === 'tu_password_app');
    
    if ($needsConfig) {
        echo "<p class='warning'>⚠️ Las credenciales SMTP aún contienen valores de ejemplo. Necesitas configurar:</p>";
        echo "<ul class='warning'>";
        echo "<li>smtp.username: tu email real</li>";
        echo "<li>smtp.password: tu contraseña de aplicación</li>";
        echo "<li>from.email: tu email de envío</li>";
        echo "<li>app.url: la URL de tu aplicación</li>";
        echo "</ul>";
    } else {
        showTestResult("Credenciales SMTP configuradas", true);
    }
} else {
    showTestResult("Archivo de configuración", false, "No existe includes/email_config.php");
}
echo "</div>";

// 5. Prueba de funciones auxiliares
echo "<div class='test-section'>";
echo "<h3>5. Verificación de Funciones</h3>";

if (file_exists('includes/email_service.php')) {
    include_once 'includes/email_service.php';
    
    // Probar generación de token
    $token = generateSecureToken();
    showTestResult("Generación de token seguro", !empty($token) && strlen($token) === 64);
    
    // Probar validación de email
    showTestResult("Validación de email válido", isValidEmail('test@ejemplo.com'));
    showTestResult("Validación de email inválido", !isValidEmail('email_invalido'));
    
    // Probar creación de servicio de email
    try {
        $emailService = new EmailService();
        showTestResult("Instancia de EmailService", true);
    } catch (Exception $e) {
        showTestResult("Instancia de EmailService", false, $e->getMessage());
    }
}
echo "</div>";

// 6. Prueba de URLs
echo "<div class='test-section'>";
echo "<h3>6. Enlaces del Sistema</h3>";

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
           '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);

$links = [
    'forgot_password.php' => 'Solicitar recuperación',
    'reset_password.php?token=test123' => 'Reset con token de prueba',
    'login.php' => 'Página de login',
];

echo "<p class='info'>URLs del sistema:</p>";
foreach ($links as $link => $description) {
    echo "<p>• <strong>$description:</strong><br>";
    echo "<span class='code'>$baseUrl/$link</span></p>";
}
echo "</div>";

// 7. Instrucciones de configuración
echo "<div class='test-section'>";
echo "<h3>7. Instrucciones de Configuración</h3>";

echo "<h4>Para configurar el email (Gmail):</h4>";
echo "<ol>";
echo "<li>Ve a tu cuenta de Google → Seguridad</li>";
echo "<li>Activa la verificación en 2 pasos</li>";
echo "<li>Genera una 'Contraseña de aplicación' para este sistema</li>";
echo "<li>Edita <code>includes/email_config.php</code> con tus credenciales</li>";
echo "<li>Cambia la URL base de la aplicación</li>";
echo "</ol>";

echo "<h4>Para probar el sistema:</h4>";
echo "<ol>";
echo "<li>Configura las credenciales SMTP</li>";
echo "<li>Ve a <a href='forgot_password.php'>forgot_password.php</a></li>";
echo "<li>Ingresa un email válido de un usuario existente</li>";
echo "<li>Revisa tu bandeja de entrada</li>";
echo "<li>Haz clic en el enlace del email</li>";
echo "<li>Cambia tu contraseña</li>";
echo "</ol>";
echo "</div>";

// 8. Información adicional
echo "<div class='test-section'>";
echo "<h3>8. Información Adicional</h3>";

echo "<p><strong>Versión PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>";

if (function_exists('openssl_get_version')) {
    echo "<p><strong>OpenSSL:</strong> " . openssl_version_text() . "</p>";
} else {
    echo "<p class='error'>⚠️ OpenSSL no está disponible (necesario para SMTP seguro)</p>";
}
echo "</div>";

// Botones de acción
echo "<div style='margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 5px;'>";
echo "<h3>Acciones Rápidas</h3>";
echo "<p>";
echo "<a href='forgot_password.php' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-right: 10px;'>🔑 Probar Recuperación</a>";
echo "<a href='login.php' style='background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; margin-right: 10px;'>🔐 Ir al Login</a>";
echo "<a href='system_verification.php' style='background: #6c757d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;'>🔧 Verificación General</a>";
echo "</p>";
echo "</div>";

echo "<hr style='margin: 30px 0;'>";
echo "<p style='text-align: center; color: #666;'>Sistema de Recuperación de Contraseña - Pruebas completadas</p>";
?>
