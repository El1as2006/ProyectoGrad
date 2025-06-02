<?php
session_start();

// Guardar tipo de usuario antes de destruir sesión
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;

// Cerrar sesión
session_unset();
session_destroy();

// Redirigir según rol
if ($rol === 'admin') {
    header("Location: login.php");
} else {
    header("Location: /ProyectoGrad/index.php");
}
exit;
