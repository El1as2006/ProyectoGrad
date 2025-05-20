<?php
$host = 'localhost'; 
$dbname = 'biblioteca';
$username = 'root'; 
$password = 'Info2025/*-'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

$query = $_GET['query'] ?? '';

if ($query) {
    $sql = "SELECT id_usuario, nombre, gmail_institucional FROM usuarios WHERE rol = 'estudiante' AND (nombre LIKE :query OR gmail_institucional LIKE :query)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->execute();
    $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($estudiantes as $estudiante) {
        echo '<a href="#" class="list-group-item list-group-item-action" data-id="' . $estudiante['id_usuario'] . '">' . htmlspecialchars($estudiante['nombre']) . ' - ' . htmlspecialchars($estudiante['gmail_institucional']) . '</a>';
    }
}
?>