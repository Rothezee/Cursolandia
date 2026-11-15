<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/conn.php';

$base = '..';
$navPage = 'perfil';
$usuarioId = (int) $_SESSION['usuario_id'];

$usuario = $conn->query("SELECT plan, plan_vencimiento, rol FROM usuarios WHERE id = $usuarioId")->fetch();
$esPro = $usuario['plan'] === 'pro';
$esAdmin = $usuario['rol'] === 'admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Mi perfil — Cursolandia</title>
</head>
<body class="page-login">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="login-main">
        <section class="login-card glass-panel glass-panel--hero">
            <header class="login-card__header">
                <h1 class="login-card__title">Hola, <?= htmlspecialchars($_SESSION['usuario'] ?? 'usuario') ?></h1>
                <p class="login-card__subtitle">Desde acá gestionás tus cursos y tu plan.</p>
            </header>

            <p class="auth-hint <?= $esPro ? 'is-ok' : '' ?>">
                <?php if ($esPro) : ?>
                    Plan Pro activo<?= $usuario['plan_vencimiento'] ? ' hasta el ' . date('d/m/Y', strtotime($usuario['plan_vencimiento'])) : '' ?>.
                <?php else : ?>
                    Plan gratuito.
                    <a href="solicitar-pro.php" class="auth-link">Solicitar Pro</a>
                <?php endif; ?>
            </p>

            <p class="login-card__footer">
                <a href="mis-cursos.php" class="auth-link">Mis cursos</a>
                ·
                <a href="crear-curso.php" class="auth-link">Crear curso</a>
                ·
                <a href="planes.php" class="auth-link">Planes</a>
            </p>

            <?php if ($esAdmin) : ?>
                <p class="login-card__footer">
                    <a href="admin-pro.php" class="auth-link">Admin Pro</a>
                </p>
            <?php endif; ?>

            <p class="login-card__footer">
                <a href="../index.php?salir=1" class="auth-link">Cerrar sesión</a>
            </p>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
