<?php
session_start();
require_once __DIR__ . '/../includes/conn.php';

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if ($usuario === '' || $password === '') {
    header('Location: login.php');
    exit;
}

$sql = 'SELECT id, usuario FROM usuarios WHERE usuario = ? AND password = ? LIMIT 1';
$stmt = $conn->prepare($sql);
$stmt->execute([$usuario, $password]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: login.php?error=1');
    exit;
}

$_SESSION['usuario_id'] = (int) $user['id'];
$_SESSION['usuario'] = $user['usuario'];

header('Location: ../index.php');
exit;
