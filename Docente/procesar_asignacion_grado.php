<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

var_dump($_POST); 

$grado = $_POST['grado'] ?? '';
$seccion = $_POST['seccion'] ?? '';
$libro_id = $_POST['libro_id'] ?? '';

if ($grado === '' || $seccion === '' || $libro_id === '') {
    
    var_dump($grado);
    var_dump($seccion);
    var_dump($libro_id);
    die("Datos incompletos.");
}


$stmtEstudiantes = $conn->prepare("
    SELECT id 
    FROM estudiantes
    WHERE grado = ? AND seccion = ?
");
$stmtEstudiantes->bind_param("ss", $grado, $seccion);
$stmtEstudiantes->execute();
$result = $stmtEstudiantes->get_result();

$estudiantes = [];
while ($row = $result->fetch_assoc()) {
    $estudiantes[] = $row['id'];
}

$stmtEstudiantes->close();

if (empty($estudiantes)) {
    die("<script>alert('No hay estudiantes en este grupo.'); window.location.href='list_grados.php';</script>");
}

$fecha_prestamo = date('Y-m-d');
$status = "asignacion";
$origen = "docente";

$id_docente = $_SESSION['user_id'];

$stmtInsert = $conn->prepare("
    INSERT INTO prestamos 
    (id_libro, fecha_prestamo, status, origen_usuario, id_estudiante, tipo_usuario, id_usuario) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");


$asignados = 0;
foreach ($estudiantes as $id_estudiante) {
    $tipo_usuario = "estudiante"; 
    $stmtInsert->bind_param("isssisi", 
        $libro_id, 
        $fecha_prestamo, 
        $status, 
        $origen, 
        $id_estudiante, 
        $tipo_usuario, 
        $id_docente
    );

    if ($stmtInsert->execute()) {
        $asignados++;
    } else {
        echo "Error: " . $stmtInsert->error . "<br>";
    }
}


$stmtInsert->close();
$conn->close();

echo "<script>
         alert('📚 Libro/lectura asignado correctamente a $asignados estudiantes del grado $grado $seccion');
         // Opción 1: volver al listado general
         window.location.href='list_grados.php';
         // Opción 2: volver al mismo grado/sección (descomenta esta línea si prefieres eso)
         //window.location.href='asignar_libro_grado.php?grado=$grado&seccion=$seccion';
       </script>";