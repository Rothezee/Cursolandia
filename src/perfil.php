<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$base = '..';
$navPage = 'perfil';
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
                <p class="login-card__subtitle">Acá vas a poder editar tu perfil, cursos y plan Pro.</p>
            </header>
            <p class="login-card__footer">
                <a href="salir.php" class="auth-link">Cerrar sesión</a>
            </p>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
