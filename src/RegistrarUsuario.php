<?php
require_once __DIR__ . '/../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$usuario = trim($_POST['usuario'] ?? '');
$intereses = trim($_POST['intereses'] ?? '');
$antecedentes = trim($_POST['antecedentes'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

if ($password !== $passwordConfirm) {
    header('Location: registro.php?error=password');
    exit;
}

$avatar = null;
if (
    isset($_FILES['avatar'])
    && ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
) {
    $avatar = $_FILES['avatar']['name'];
}

$sql = 'INSERT INTO usuarios (
    nombre, apellido, email, telefono, usuario,
    intereses, antecedentes, avatar, password
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $nombre,
        $apellido,
        $email,
        $telefono !== '' ? $telefono : null,
        $usuario,
        $intereses !== '' ? $intereses : null,
        $antecedentes !== '' ? $antecedentes : null,
        $avatar,
        $password,
    ]);
} catch (PDOException $e) {
    if ((int) $e->errorInfo[1] === 1062) {
        header('Location: registro.php?error=duplicado');
        exit;
    }
    throw $e;
}

header('Location: login.php?registro=1');
exit;
