<?php
// Iniciar buffer de salida y limpiar cualquier contenido previo
ob_start();
ob_clean();

// Configurar headers antes de cualquier salida
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que es una petición AJAX válida
if (!isset($_GET['ajax_notificaciones'])) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

// Obtener ID de usuario de la sesión
$id_usuario = null;
if (!empty($_SESSION['user_id'])) {
    $id_usuario = $_SESSION['user_id'];
} elseif (!empty($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
}

// Inicializar array de notificaciones
$notificaciones = [];

// Configuración de base de datos directa (sin include)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteca";

try {
    // Crear conexión directa
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset("utf8");
    
    // Si tenemos usuario, obtener notificaciones
    if ($id_usuario) {
        $stmt = $conn->prepare('SELECT tipo, mensaje, fecha, leido FROM notificaciones WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 5');
        if ($stmt) {
            $stmt->bind_param('i', $id_usuario);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $row['fecha'] = date('d/m/Y H:i', strtotime($row['fecha']));
                $notificaciones[] = $row;
            }
            $stmt->close();
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    // En caso de error, devolver array vacío
    $notificaciones = [];
}

// Limpiar cualquier salida previa y enviar JSON
ob_clean();
echo json_encode($notificaciones, JSON_UNESCAPED_UNICODE);
exit;
?>