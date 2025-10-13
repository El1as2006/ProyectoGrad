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

$id = $_GET['id'] ?? '';

if ($id) {
    $sql = "SELECT archivo_pdf FROM libros WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $libro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($libro) {
        $pdfContent = $libro['archivo_pdf'];
        $pdfPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($pdfPath, $pdfContent);

        $imagick = new Imagick();
        $imagick->setResolution(150, 150);
        $imagick->readImage($pdfPath . '[0]');
        $imagick->setImageFormat('jpeg');
        $imagick->setImageCompressionQuality(90);

        header('Content-Type: image/jpeg');
        echo $imagick;
        unlink($pdfPath);
    } else {
        echo "Archivo no encontrado.";
    }
} else {
    echo "ID no proporcionado.";
}
?>