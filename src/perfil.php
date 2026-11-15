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
$hoy = date('Y-m-d');

$usuario = $conn->query("SELECT plan, plan_vencimiento, rol FROM usuarios WHERE id = $usuarioId")->fetch();
$esPro = $usuario['plan'] === 'pro';
$esAdmin = $usuario['rol'] === 'admin';

$stmt = $conn->prepare('SELECT c.id, c.titulo, c.fecha_inicio, c.fecha_fin, c.costo,
        i.estado, u.nombre, u.apellido
    FROM inscripciones i
    JOIN cursos c ON c.id = i.curso_id
    JOIN usuarios u ON u.id = c.creador_id
    WHERE i.usuario_id = ?
    ORDER BY c.fecha_inicio DESC');
$stmt->execute([$usuarioId]);
$inscripciones = $stmt->fetchAll();

function fechaCorta($fecha){
    return $fecha ? date('d/m/Y', strtotime($fecha)) : '—';
}

function estadoCurso($inicio, $fin, $hoy){
    if ($hoy < $inicio) {
        return 'Próximo';
    }
    if ($hoy > $fin) {
        return 'Finalizado';
    }
    return 'Activo';
}
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
                <a href="#mis-inscripciones" class="auth-link">Mis inscripciones</a>
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

            <div id="mis-inscripciones">
                <h2 class="curso-info__title">Mis inscripciones</h2>

                <?php if (!$inscripciones) : ?>
                    <p class="empty-state empty-state--compact">
                        Todavía no te inscribiste en ningún curso.
                        <a class="auth-link" href="cursos.php">Ver el catálogo</a>
                    </p>
                <?php else : ?>
                    <ul class="forum-thread-list">
                        <?php foreach ($inscripciones as $insc) : ?>
                            <?php
                            $confirmada = $insc['estado'] === 'confirmada';
                            $docente = $insc['nombre'] . ' ' . $insc['apellido'];
                            ?>
                            <li class="forum-thread-item">
                                <article class="forum-thread card">
                                    <div class="forum-thread__badges">
                                        <span class="forum-badge forum-badge--<?= $confirmada ? 'abierta' : 'cerrada' ?>">
                                            <?= $confirmada ? 'Confirmada' : 'Pendiente' ?>
                                        </span>
                                        <span class="course-badge course-badge--<?= $insc['costo'] > 0 ? 'pago' : 'gratis' ?>">
                                            <?= $insc['costo'] > 0 ? 'Pago' : 'Gratis' ?>
                                        </span>
                                    </div>
                                    <h3 class="forum-thread__title">
                                        <a href="curso.php?id=<?= (int) $insc['id'] ?>"><?= htmlspecialchars($insc['titulo']) ?></a>
                                    </h3>
                                    <div class="forum-thread__meta">
                                        <span>Docente: <strong><?= htmlspecialchars($docente) ?></strong></span>
                                        <span><?= fechaCorta($insc['fecha_inicio']) ?> → <?= fechaCorta($insc['fecha_fin']) ?></span>
                                        <span><?= estadoCurso($insc['fecha_inicio'], $insc['fecha_fin'], $hoy) ?></span>
                                    </div>
                                </article>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <p class="login-card__footer">
                <a href="../index.php?salir=1" class="auth-link">Cerrar sesión</a>
            </p>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>