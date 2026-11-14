<?php
session_start();
require_once __DIR__ . '/../includes/conn.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$base = '..';
$navPage = 'mis-cursos';
$usuarioId = (int) $_SESSION['usuario_id'];

$usuario = $conn->query("SELECT usuario, plan FROM usuarios WHERE id = $usuarioId")->fetch();

$misCursos = $conn->query("SELECT c.*,
        (SELECT COUNT(*) FROM inscripciones i WHERE i.curso_id = c.id AND i.estado = 'confirmada') AS inscriptos,
        (SELECT COUNT(*) FROM preguntas p WHERE p.curso_id = c.id) AS preguntas
    FROM cursos c
    WHERE c.creador_id = $usuarioId
    ORDER BY c.fecha_inicio DESC, c.id DESC")->fetchAll();

$hoy = date('Y-m-d');
$noVencidos = 0;
foreach ($misCursos as $curso) {
    if ($curso['fecha_fin'] >= $hoy) {
        $noVencidos++;
    }
}

$esPro = $usuario['plan'] === 'pro';
$limite = 3;
$puedeCrear = $esPro || $noVencidos < $limite;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Mis cursos — Cursolandia</title>
</head>
<body class="page-cursos">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell">
        <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta">
            <a class="text-link" href="../index.php">Inicio</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <span>Mis cursos</span>
        </nav>

        <header class="section-header split-header page-cursos-header">
            <div>
                <p class="section-eyebrow">Tus cursos</p>
                <h1 class="section-title">Mis cursos</h1>
                <p class="section-lead">
                    Cursos que creaste. Podés revisar su estado, fechas y la actividad del foro.
                </p>
                <p class="auth-hint">
                    Plan <?= $esPro ? 'Pro' : 'gratuito' ?>:
                    <?php if ($esPro) : ?>
                        sin tope de cursos activos.
                    <?php else : ?>
                        <?= $noVencidos ?> de <?= $limite ?> cursos activos.
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($puedeCrear) : ?>
                <a href="crear-curso.php" class="auth-submit auth-submit--link">
                    <span class="auth-submit__text">Crear curso</span>
                    <span class="auth-submit__shine" aria-hidden="true"></span>
                </a>
            <?php else : ?>
                <p class="auth-hint">
                    Alcanzaste el tope del plan gratuito. Solicitá el plan Pro para crear más cursos.
                </p>
            <?php endif; ?>
        </header>

        <div class="card-grid card-grid--catalog">
            <?php if (!$misCursos) : ?>
                <p class="empty-state empty-state--grid">
                    Todavía no creaste ningún curso. Usá el botón «Crear curso» para publicar el primero.
                </p>
            <?php endif; ?>

            <?php foreach ($misCursos as $curso) : ?>
                <?php
                $esPago = $curso['costo'] > 0;

                if ($hoy < $curso['fecha_inicio']) {
                    $estado = 'Próximamente';
                    $estadoClase = 'proximo';
                } elseif ($hoy <= $curso['fecha_fin']) {
                    $estado = 'Activo';
                    $estadoClase = 'activo';
                } else {
                    $estado = 'Finalizado';
                    $estadoClase = 'finalizado';
                }
                ?>
                <article class="card course-card<?= $esPro ? ' card-accent-pro' : '' ?>">
                    <div class="course-card__badges">
                        <span class="course-badge course-badge--<?= $estadoClase ?>"><?= $estado ?></span>
                        <span class="course-badge course-badge--<?= $esPago ? 'pago' : 'gratis' ?>">
                            <?= $esPago ? 'Pago' : 'Gratis' ?>
                        </span>
                        <?php if ($esPro) : ?>
                            <span class="course-badge course-badge--pro">Destacado Pro</span>
                        <?php endif; ?>
                    </div>

                    <h2 class="card-title course-card__title"><?= htmlspecialchars($curso['titulo']) ?></h2>
                    <p class="card-text"><?= htmlspecialchars($curso['descripcion']) ?></p>

                    <p class="course-card__meta">
                        Del <?= date('d/m/Y', strtotime($curso['fecha_inicio'])) ?>
                        al <?= date('d/m/Y', strtotime($curso['fecha_fin'])) ?>
                        · <?= (int) $curso['inscriptos'] ?> inscriptos
                        · <?= (int) $curso['preguntas'] ?> preguntas
                        · <?= $curso['acceso'] === 'publico' ? 'Público' : 'Privado' ?>
                    </p>

                    <a href="curso.php?id=<?= (int) $curso['id'] ?>" class="btn-outline btn-outline--cyan course-card__cta">
                        Ver curso
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
