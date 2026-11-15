<?php
require __DIR__ . '/config.php';

$dsn = "mysql:host={$servername};dbname={$dbname};charset=utf8mb4";

try {
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Conexión fallida: ' . $e->getMessage());
}

// Si un plan Pro venció, vuelve automáticamente al plan gratuito.
$conn->query("UPDATE usuarios SET plan = 'gratuito', plan_vencimiento = NULL
    WHERE plan = 'pro' AND plan_vencimiento IS NOT NULL AND plan_vencimiento < CURDATE()");
