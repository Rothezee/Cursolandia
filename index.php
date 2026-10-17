<?php
session_start();
$base = '';
$navPage = 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css"/>
    <link rel="icon" href="img/logo.svg" type="image/x-icon">
    <title>Cursolandia — Comunidad, cursos y foro Q&amp;A</title>
</head>
<body class="page-home">
    <?php require __DIR__ . '/includes/nav.php'; ?>

    <main id="inicio">
        <div id="sections-root" class="sections-root" aria-live="polite"></div>
    </main>

    <?php require __DIR__ . '/includes/footer.php'; ?>
    <script src="scripts/home-sections.js" defer></script>
</body>
</html>
