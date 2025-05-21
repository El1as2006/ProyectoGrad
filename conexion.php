<?php
$host = 'localhost';
$dbname = 'bibliotecachaleca';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}