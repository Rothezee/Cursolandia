<?php
session_start();
require_once __DIR__ . '/../includes/conn.php';

$base = '..';
$navPage = 'cursos';

$id = (int) ($_GET['id'] ?? 0);
$curso = null;
$tagsCurso = [];
$materiales = [];
$preguntas = [];
$inscriptos = 0;

if ($id > 0) {
    $stmt = $conn->prepare('SELECT c.*, u.nombre, u.apellido, u.usuario, u.plan
        FROM cursos c
        JOIN usuarios u ON u.id = c.creador_id
        WHERE c.id = ?');
    $stmt->execute([$id]);
    $curso = $stmt->fetch();

    if ($curso) {
        $stmt = $conn->prepare('SELECT e.nombre
            FROM curso_etiquetas ce
            JOIN etiquetas e ON e.id = ce.etiqueta_id
            WHERE ce.curso_id = ?
            ORDER BY e.nombre');
        $stmt->execute([$id]);
        $tagsCurso = $stmt->fetchAll();

        $stmt = $conn->prepare('SELECT nombre, ruta, tamano FROM materiales WHERE curso_id = ? ORDER BY nombre');
        $stmt->execute([$id]);
        $materiales = $stmt->fetchAll();

        $stmt = $conn->prepare("SELECT p.id, p.titulo, p.cuerpo, p.estado, p.creada_en, u.usuario AS autor,
                (SELECT COUNT(*) FROM respuestas r WHERE r.pregunta_id = p.id) AS respuestas,
                (SELECT COUNT(*) FROM respuestas r WHERE r.pregunta_id = p.id AND r.aceptada = 1) AS aceptadas
            FROM preguntas p
            JOIN usuarios u ON u.id = p.autor_id
            WHERE p.curso_id = ?
            ORDER BY p.creada_en DESC");
        $stmt->execute([$id]);
        $preguntas = $stmt->fetchAll();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM inscripciones WHERE curso_id = ? AND estado = 'confirmada'");
        $stmt->execute([$id]);
        $inscriptos = (int) $stmt->fetchColumn();
    }
}

function fechaCorta($fecha)
{
    return $fecha ? date('d/m/Y', strtotime($fecha)) : '—';
}

function tamanoLegible($bytes)
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024) . ' KB';
    }
    return $bytes . ' B';
}

$docente = $curso ? $curso['nombre'] . ' ' . $curso['apellido'] : '';
$iniciales = $curso ? strtoupper(substr($curso['nombre'], 0, 1) . substr($curso['apellido'], 0, 1)) : '';
$esPago = $curso ? $curso['costo'] > 0 : false;
$activo = $curso && date('Y-m-d') >= $curso['fecha_inicio'] && date('Y-m-d') <= $curso['fecha_fin'];
$hayCupo = $curso && ($curso['cupo'] === null || $inscriptos < $curso['cupo']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title><?= $curso ? htmlspecialchars($curso['titulo']) . ' — Cursolandia' : 'Curso — Cursolandia' ?></title>
</head>
<body class="page-curso">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell page-shell--compact">
        <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta del curso">
            <a class="text-link" href="cursos.php">Cursos</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <span><?= $curso ? htmlspecialchars($curso['titulo']) : 'Curso' ?></span>
        </nav>

        <?php if ($curso && isset($_GET['creado'])) : ?>
            <p class="auth-hint is-ok" role="status">Curso publicado correctamente.</p>
        <?php endif; ?>

        <?php if (!$curso) : ?>
            <p class="empty-state empty-state--compact">No encontramos ese curso. Volvé al catálogo y elegí uno.</p>
        <?php else : ?>
            <header class="curso-hero">
                <div class="curso-hero__glow" aria-hidden="true"></div>
                <div class="curso-hero__inner card glass-panel">
                    <div class="course-card__badges">
                        <?php if ($curso['plan'] === 'pro') : ?>
                            <span class="course-badge course-badge--pro">Destacado Pro</span>
                        <?php endif; ?>
                        <span class="course-badge course-badge--<?= $esPago ? 'pago' : 'gratis' ?>"><?= $esPago ? 'Pago' : 'Gratis' ?></span>
                        <span class="forum-badge forum-badge--abierta"><?= $curso['acceso'] === 'publico' ? 'Público' : 'Privado' ?></span>
                    </div>
                    <h1 class="curso-hero__title"><?= htmlspecialchars($curso['titulo']) ?></h1>
                    <p class="curso-hero__desc"><?= htmlspecialchars($curso['descripcion']) ?></p>
                    <ul class="course-card__tags">
                        <?php foreach ($tagsCurso as $tag) : ?>
                            <li><?= htmlspecialchars($tag['nombre']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </header>

            <div class="curso-layout">
                <div class="curso-layout__main">
                    <div class="curso-tabs" role="tablist" aria-label="Secciones del curso">
                        <button type="button" class="curso-tab is-active" role="tab" id="tab-info" aria-selected="true" aria-controls="panel-info" data-tab="info">
                            Información
                        </button>
                        <button type="button" class="curso-tab" role="tab" id="tab-material" aria-selected="false" aria-controls="panel-material" data-tab="material">
                            Material
                        </button>
                        <button type="button" class="curso-tab" role="tab" id="tab-foro" aria-selected="false" aria-controls="panel-foro" data-tab="foro">
                            Foro Q&amp;A
                        </button>
                    </div>

                    <section class="curso-panel" id="panel-info" role="tabpanel" aria-labelledby="tab-info">
                        <div class="curso-info card">
                            <h2 class="curso-info__title">Sobre este curso</h2>
                            <p class="curso-info__body"><?= htmlspecialchars($curso['descripcion']) ?></p>
                        </div>
                        <div class="curso-stats card">
                            <h2 class="curso-info__title">Detalles de vigencia y acceso</h2>
                            <dl class="curso-dl">
                                <dt>Vigencia</dt>
                                <dd><?= fechaCorta($curso['fecha_inicio']) ?> → <?= fechaCorta($curso['fecha_fin']) ?></dd>
                                <dt>Estado del curso</dt>
                                <dd><?= $activo ? 'Activo' : 'Finalizado' ?></dd>
                                <dt>Acceso</dt>
                                <dd><?= $curso['acceso'] === 'publico' ? 'Público' : 'Privado' ?></dd>
                                <dt>Modalidad de pago</dt>
                                <dd><?= $esPago ? 'Pago (acordar con el docente)' : 'Gratis' ?></dd>
                                <dt>Cupo</dt>
                                <dd><?= $curso['cupo'] === null ? 'Sin límite' : $inscriptos . ' / ' . $curso['cupo'] . ' inscriptos' ?></dd>
                            </dl>
                        </div>
                        <div class="curso-docente card">
                            <h2 class="curso-info__title">Docente</h2>
                            <div class="curso-docente__row">
                                <div class="curso-docente__avatar" aria-hidden="true"><?= htmlspecialchars($iniciales) ?></div>
                                <div>
                                    <p class="curso-docente__name"><?= htmlspecialchars($docente) ?></p>
                                    <p class="curso-docente__hint">Creador del curso y moderador del foro Q&amp;A.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="curso-panel" id="panel-material" role="tabpanel" aria-labelledby="tab-material" hidden>
                        <div class="curso-material card">
                            <h2 class="curso-info__title">Archivos del curso</h2>
                            <p class="auth-hint">Material subido por el docente. En plan gratuito aplican límites de cantidad y tamaño por archivo.</p>
                            <ul class="curso-material-list">
                                <?php if (!$materiales) : ?>
                                    <li class="empty-state empty-state--compact">El docente aún no publicó archivos en este curso.</li>
                                <?php endif; ?>
                                <?php foreach ($materiales as $archivo) : ?>
                                    <li class="curso-material-item">
                                        <span class="curso-material-item__icon" aria-hidden="true"></span>
                                        <div>
                                            <span class="curso-material-item__name"><?= htmlspecialchars($archivo['nombre']) ?></span>
                                            <span class="curso-material-item__size"><?= tamanoLegible((int) $archivo['tamano']) ?></span>
                                        </div>
                                        <button type="button" class="forum-action-btn" disabled title="Disponible al conectar archivos en el servidor">Descargar</button>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </section>

                    <section class="curso-panel" id="panel-foro" role="tabpanel" aria-labelledby="tab-foro" hidden>
                        <header class="curso-foro-header split-header">
                            <div>
                                <h2 class="curso-foro-header__title">Foro del curso</h2>
                                <p class="section-lead curso-foro-header__lead">
                                    Preguntas y respuestas solo para participantes inscriptos. Respuestas aceptadas y moderación docente.
                                </p>
                            </div>
                            <button type="button" class="forum-new-btn" disabled title="Próximamente: requiere iniciar sesión">
                                Nueva pregunta
                            </button>
                        </header>
                        <div class="glass-panel toolbar" role="search">
                            <label class="courses-search" for="forum-search">
                                <span class="courses-search__label">Buscar en este foro</span>
                                <input type="search" id="forum-search" class="courses-search__input field-control" placeholder="Título o etiqueta…" autocomplete="off" />
                            </label>
                            <div class="forum-toolbar__filters courses-filters">
                                <div class="courses-filters__row">
                                    <div class="filter-field filter-field--chips">
                                        <span class="filter-field__label">Estado</span>
                                        <div id="filter-forum-status" class="filter-chips" role="group" aria-label="Estado del hilo">
                                            <button type="button" class="filter-chip is-active" data-status="todos">Todas</button>
                                            <button type="button" class="filter-chip" data-status="abierta">Abiertas</button>
                                            <button type="button" class="filter-chip" data-status="cerrada">Cerradas</button>
                                        </div>
                                    </div>
                                    <button type="button" id="forum-filters-reset" class="filters-reset">Limpiar</button>
                                </div>
                            </div>
                        </div>
                        <p id="forum-count" class="results-count courses-results-count" aria-live="polite"></p>
                        <ul id="forum-thread-list" class="forum-thread-list" aria-live="polite">
                            <?php if (!$preguntas) : ?>
                                <li class="empty-state">No hay preguntas en este curso con esos filtros.</li>
                            <?php endif; ?>
                            <?php foreach ($preguntas as $pregunta) : ?>
                                <?php
                                $respuestas = (int) $pregunta['respuestas'];
                                $rol = $pregunta['autor'] === $curso['usuario'] ? 'docente' : 'alumno';
                                ?>
                                <li class="forum-thread-item" data-titulo="<?= htmlspecialchars($pregunta['titulo']) ?>" data-cuerpo="<?= htmlspecialchars($pregunta['cuerpo']) ?>" data-estado="<?= $pregunta['estado'] ?>">
                                    <article class="forum-thread card">
                                        <div class="forum-thread__badges">
                                            <span class="forum-badge forum-badge--<?= $pregunta['estado'] ?>"><?= $pregunta['estado'] === 'cerrada' ? 'Cerrada' : 'Abierta' ?></span>
                                            <?php if ((int) $pregunta['aceptadas'] > 0) : ?>
                                                <span class="forum-badge forum-badge--accepted">Respuesta aceptada</span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="forum-thread__title">
                                            <a href="foro-hilo.php?curso=<?= (int) $curso['id'] ?>&id=<?= (int) $pregunta['id'] ?>"><?= htmlspecialchars($pregunta['titulo']) ?></a>
                                        </h3>
                                        <p class="forum-thread__excerpt"><?= htmlspecialchars($pregunta['cuerpo']) ?></p>
                                        <div class="forum-thread__meta">
                                            <span>Por <strong><?= htmlspecialchars($pregunta['autor']) ?></strong> · <?= $rol ?></span>
                                            <span><?= $respuestas ?> respuesta<?= $respuestas === 1 ? '' : 's' ?></span>
                                            <span><?= fechaCorta($pregunta['creada_en']) ?></span>
                                        </div>
                                    </article>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>

                <aside class="curso-sidebar card">
                    <p class="curso-sidebar__price"><?= $esPago ? 'Curso pago' : 'Gratis' ?></p>
                    <p class="curso-sidebar__access"><?= $curso['acceso'] === 'publico' ? 'Público' : 'Privado' ?></p>
                    <button type="button" class="auth-submit curso-enroll-btn" disabled title="Próximamente con sesión iniciada">
                        <span class="auth-submit__text">
                            <?= !$hayCupo ? 'Cupo completo' : ($esPago ? 'Solicitar inscripción' : 'Inscribirme') ?>
                        </span>
                        <span class="auth-submit__shine" aria-hidden="true"></span>
                    </button>
                    <p class="curso-sidebar__note">
                        <?php if (!$hayCupo) : ?>
                            No quedan lugares. Consultá otras fechas o cursos.
                        <?php elseif ($esPago) : ?>
                            En cursos pagos la inscripción queda pendiente hasta que el docente confirme el pago (gestión externa).
                        <?php else : ?>
                            Necesitás iniciar sesión para anotarte (próximamente).
                        <?php endif; ?>
                    </p>
                    <ul class="curso-sidebar__stats">
                        <li><span>Inscriptos</span><strong><?= $inscriptos ?></strong></li>
                        <li><span>Preguntas en el foro</span><strong><?= count($preguntas) ?></strong></li>
                        <li><span>Etiquetas</span><strong><?= count($tagsCurso) ?></strong></li>
                    </ul>
                    <button type="button" class="btn-outline btn-outline--green curso-sidebar__foro-link" id="curso-jump-foro">
                        Ir al foro del curso
                    </button>
                </aside>
            </div>
        <?php endif; ?>
    </main>

    <script src="../scripts/curso.js" defer></script>
    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
