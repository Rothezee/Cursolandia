<?php
$servername = 'localhost';
$dbname = 'cursolandia';
$username = 'root';
$password = ''; 

$dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";

try {
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Conexión fallida: ' . $e->getMessage());
}
