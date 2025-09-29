<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../conexion.php';

// Recibir datos del formulario
$grado = $_POST['grado'] ?? '';
$seccion = $_POST['seccion'] ?? '';
$libro_id = $_POST['libro_id'] ?? '';

if ($grado === '' || $seccion === '' || $libro_id === '') {
    die("Datos incompletos.");
}

// Obtener estudiantes del grupo
$stmtEstudiantes = $conn->prepare("SELECT id FROM estudiantes WHERE grado = ? AND seccion = ?");
$stmtEstudiantes->bind_param("ss", $grado, $seccion);
$stmtEstudiantes->execute();
$result = $stmtEstudiantes->get_result();

$estudiantes = [];
while ($row = $result->fetch_assoc()) {
    $estudiantes[] = $row['id'];
}
$stmtEstudiantes->close();

if (empty($estudiantes)) {
    die("No hay estudiantes en este grupo.");
}

// Validar que todos los estudiantes existan en la tabla usuarios
$todos_existen = true;
foreach ($estudiantes as $id_estudiante) {
    $stmtCheckUser = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE id_usuario = ?");
    $stmtCheckUser->bind_param("i", $id_estudiante);
    $stmtCheckUser->execute();
    $stmtCheckUser->bind_result($countUser);
    $stmtCheckUser->fetch();
    $stmtCheckUser->close();

    if ($countUser == 0) {
        $todos_existen = false;
        break;
    }
}

if (!$todos_existen) {
    die("<script>alert('Error: No todos los estudiantes existen en la base de datos. No se realizó ninguna asignación.'); 
         window.location.href='list_grados.php';</script>");
}

// Configuración de la asignación
$fecha_prestamo = date('Y-m-d');
$status = "asignado";  // Asignación virtual
$origen = "docente";
$tipo_prestamo = 1; // 1 = Asignación, 0 = Préstamo

// Contador de asignaciones realizadas
$asignados = 0;

foreach ($estudiantes as $id_estudiante) {
    // Preparar INSERT para cada estudiante
    $stmtInsert = $conn->prepare("INSERT INTO prestamos (id_libro, fecha_prestamo, status, origen_usuario, id_usuario, tipo_prestamo) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("isssii", $libro_id, $fecha_prestamo, $status, $origen, $id_estudiante, $tipo_prestamo);

    if ($stmtInsert->execute()) {
        $asignados++;
    }
    $stmtInsert->close();
}

$conn->close();

// Mensaje final
echo "<script>
        alert('📚 Libro/lectura asignado correctamente a $asignados estudiantes del grado $grado° $seccion'); 
        window.location.href='list_grados.php';
      </script>";