<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

$grado = $_POST['grado'] ?? '';
$seccion = $_POST['seccion'] ?? '';
$libro_id = $_POST['libro_id'] ?? '';

if ($grado === '' || $seccion === '' || $libro_id === '') {
    // Mostrar los valores antes de detener el script
    var_dump($grado);
    var_dump($seccion);
    var_dump($libro_id);
    die("Datos incompletos."); // Se detiene después de mostrar los valores
}


// Obtener estudiantes del grupo vinculados con usuarios
$stmtEstudiantes = $conn->prepare("
    SELECT u.id_usuario 
    FROM estudiantes e
    INNER JOIN usuarios u ON u.id_usuario = e.id  -- ajusta esta relación si es diferente
    WHERE e.grado = ? AND e.seccion = ?
");
$stmtEstudiantes->bind_param("ss", $grado, $seccion);
$stmtEstudiantes->execute();
$result = $stmtEstudiantes->get_result();

$estudiantes = [];
while ($row = $result->fetch_assoc()) {
    $estudiantes[] = $row['id_usuario']; // ✅ usar el campo correcto
}
$stmtEstudiantes->close();

if (empty($estudiantes)) {
    die("<script>alert('No hay estudiantes en este grupo.'); window.location.href='list_grados.php';</script>");
}

// Configuración de la asignación
$fecha_prestamo = date('Y-m-d');
$status = "asignacion";  // asignación virtual
$origen = "docente";

// Preparar el INSERT
// Preparar el INSERT una vez
$stmtInsert = $conn->prepare("INSERT INTO prestamos (id_libro, fecha_prestamo, status, origen_usuario, id_usuario) 
                              VALUES (?, ?, ?, ?, ?)");

// Contador
$asignados = 0;

foreach ($estudiantes as $id_estudiante) {
    // Bind dentro del foreach para cada estudiante
    $stmtInsert->bind_param("isssi", $libro_id, $fecha_prestamo, $status, $origen, $id_estudiante);
    if ($stmtInsert->execute()) {
        $asignados++;
    }
}

$stmtInsert->close();
$conn->close();


// Mensaje final
 echo "<script>
         alert('📚 Libro/lectura asignado correctamente a $asignados estudiantes del grado $grado° $seccion');
         window.location.href='procesar_asignacion_grado.php';
       </script>";