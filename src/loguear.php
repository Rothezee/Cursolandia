<?php
session_start();
require_once __DIR__ . '/../includes/conn.php';

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if ($usuario === '' || $password === '') {
    header('Location: login.php');
    exit;
}

$sql = 'SELECT id, usuario, password FROM usuarios WHERE usuario = ? LIMIT 1';
$stmt = $conn->prepare($sql);
$stmt->execute([$usuario]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    header('Location: login.php?error=1');
    exit;
}

$_SESSION['usuario_id'] = (int) $user['id'];
$_SESSION['usuario'] = $user['usuario'];

header('Location: ../index.php');
exit;
