<?php
// notificaciones_automaticas.php
// Script para generar notificaciones automáticas sobre préstamos activos, por vencer y vencidos

require_once '../conexion.php';

// Configuración: días antes de vencimiento para alerta
$alerta_dias = 2;
$hoy = date('Y-m-d');

// 1. Obtener todos los préstamos activos (no devueltos)
$sql = "SELECT p.id, p.id_estudiante, p.fecha_vencimiento, u.email, u.nombre
        FROM prestamos p
        JOIN usuarios u ON p.id_estudiante = u.id
        WHERE p.devuelto = 0";
$result = $conn->query($sql);

$notificaciones_generadas = 0;

while ($prestamo = $result->fetch_assoc()) {
    $id_estudiante = $prestamo['id_estudiante'];
    $fecha_vencimiento = $prestamo['fecha_vencimiento'];
    $nombre = $prestamo['nombre'];
    $dias_restantes = (strtotime($fecha_vencimiento) - strtotime($hoy)) / 86400;

    // 1. Notificación general de préstamo activo
    $mensaje = "Tienes un libro prestado con fecha de vencimiento el $fecha_vencimiento.";
    $tipo = 'prestamo_activo';
    generarNotificacion($conn, $id_estudiante, $mensaje, $tipo);
    $notificaciones_generadas++;

    // 2. Notificación de libro por vencer
    if ($dias_restantes > 0 && $dias_restantes <= $alerta_dias) {
        $mensaje = "Tu libro prestado vence en $dias_restantes día(s): $fecha_vencimiento. Por favor, devuélvelo a tiempo.";
        $tipo = 'por_vencer';
        generarNotificacion($conn, $id_estudiante, $mensaje, $tipo);
        $notificaciones_generadas++;
    }

    // 3. Notificación de libro vencido
    if ($dias_restantes < 0) {
        $mensaje = "Tienes un libro vencido desde $fecha_vencimiento. Por favor, realiza la devolución lo antes posible.";
        $tipo = 'vencido';
        generarNotificacion($conn, $id_estudiante, $mensaje, $tipo);
        $notificaciones_generadas++;
    }
}

function generarNotificacion($conn, $id_usuario, $mensaje, $tipo) {
    // Evitar notificaciones duplicadas recientes (opcional)
    $sql_check = "SELECT id FROM notificaciones WHERE id_usuario = ? AND mensaje = ? AND tipo = ? AND fecha >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param('iss', $id_usuario, $mensaje, $tipo);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $sql_insert = "INSERT INTO notificaciones (id_usuario, mensaje, tipo, fecha, leido) VALUES (?, ?, ?, NOW(), 0)";
        $stmt2 = $conn->prepare($sql_insert);
        $stmt2->bind_param('iss', $id_usuario, $mensaje, $tipo);
        $stmt2->execute();
        $stmt2->close();
    }
    $stmt->close();
}

echo "Notificaciones automáticas generadas: $notificaciones_generadas\n";
