<?php
$base = '..';
$navPage = 'planes';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Planes — Cursolandia</title>
</head>
<body class="page-planes">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell" id="planes-app" aria-live="polite">
        <p class="empty-state">Cargando planes…</p>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
    <script src="../scripts/planes.js" defer></script>
</body>
</html>
