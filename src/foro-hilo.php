<?php
session_start();
require_once __DIR__ . '/../includes/conn.php';

$base = '..';
$navPage = 'foro';

$id = (int) ($_GET['id'] ?? 0);
$error = '';
$hilo = null;
$respuestas = [];
$logueado = !empty($_SESSION['usuario_id']);

if ($id > 0) {
    $stmt = $conn->prepare('SELECT p.*, c.titulo AS curso, c.creador_id, u.usuario AS autor
        FROM preguntas p
        JOIN cursos c ON c.id = p.curso_id
        JOIN usuarios u ON u.id = p.autor_id
        WHERE p.id = ?');
    $stmt->execute([$id]);
    $hilo = $stmt->fetch();
}

if ($hilo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $cuerpo = trim($_POST['body'] ?? '');

    if (!$logueado) {
        $error = 'Necesitás iniciar sesión para responder.';
    } elseif ($hilo['estado'] === 'cerrada') {
        $error = 'El hilo está cerrado y no admite respuestas.';
    } elseif ($cuerpo === '') {
        $error = 'Escribí una respuesta antes de enviar.';
    } else {
        $stmt = $conn->prepare('SELECT autor_id FROM respuestas WHERE pregunta_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$id]);
        $ultimoAutor = $stmt->fetchColumn();

        if ($ultimoAutor !== false && (int) $ultimoAutor === (int) $_SESSION['usuario_id']) {
            $error = 'No podés responder dos veces seguidas. Editá tu última respuesta o esperá a otro participante.';
        } else {
            $stmt = $conn->prepare('INSERT INTO respuestas (pregunta_id, autor_id, cuerpo) VALUES (?, ?, ?)');
            $stmt->execute([$id, (int) $_SESSION['usuario_id'], $cuerpo]);
            header('Location: foro-hilo.php?curso=' . (int) $hilo['curso_id'] . '&id=' . $id);
            exit;
        }
    }
}

if ($hilo) {
    $stmt = $conn->prepare('SELECT r.*, u.usuario AS autor
        FROM respuestas r
        JOIN usuarios u ON u.id = r.autor_id
        WHERE r.pregunta_id = ?
        ORDER BY r.creada_en, r.id');
    $stmt->execute([$id]);
    $respuestas = $stmt->fetchAll();
}

function fechaCorta($fecha)
{
    return $fecha ? date('d/m/Y', strtotime($fecha)) : '—';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title><?= $hilo ? htmlspecialchars($hilo['titulo']) . ' — Foro' : 'Hilo — Foro del curso' ?></title>
</head>
<body class="page-foro page-foro-hilo">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell">
        <?php if (!$hilo) : ?>
            <p class="empty-state">Enlace inválido. Abrí el hilo desde el foro del curso.</p>
        <?php else : ?>
            <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta del hilo">
                <a class="text-link" href="cursos.php">Cursos</a>
                <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
                <a class="text-link" href="curso.php?id=<?= (int) $hilo['curso_id'] ?>"><?= htmlspecialchars($hilo['curso']) ?></a>
                <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
                <a class="text-link" href="curso.php?id=<?= (int) $hilo['curso_id'] ?>#foro">Foro</a>
                <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
                <span>Hilo</span>
            </nav>

            <article class="forum-question card">
                <div class="forum-thread__head">
                    <div class="forum-thread__badges">
                        <span class="forum-badge forum-badge--<?= $hilo['estado'] ?>"><?= $hilo['estado'] === 'cerrada' ? 'Cerrada' : 'Abierta' ?></span>
                    </div>
                </div>
                <h1 class="forum-question__title"><?= htmlspecialchars($hilo['titulo']) ?></h1>
                <p class="forum-question__body"><?= htmlspecialchars($hilo['cuerpo']) ?></p>
                <footer class="forum-question__meta meta-row">
                    <span>Pregunta de <strong><?= htmlspecialchars($hilo['autor']) ?></strong></span>
                    <span><?= fechaCorta($hilo['creada_en']) ?></span>
                </footer>
                <div class="forum-question__mod-actions">
                    <button type="button" class="forum-action-btn" disabled title="Docente: próximamente">Destacar</button>
                    <button type="button" class="forum-action-btn forum-action-btn--danger" disabled title="Docente: próximamente">Eliminar pregunta</button>
                </div>
            </article>

            <section class="forum-answers-section" aria-labelledby="answers-heading">
                <h2 id="answers-heading" class="forum-answers-heading">
                    Respuestas <span class="forum-answers-count">(<span><?= count($respuestas) ?></span>)</span>
                </h2>
                <div class="forum-answers">
                    <?php if (!$respuestas) : ?>
                        <p class="empty-state">Todavía no hay respuestas. Sé el primero en ayudar.</p>
                    <?php endif; ?>
                    <?php foreach ($respuestas as $respuesta) : ?>
                        <?php
                        $aceptada = (int) $respuesta['aceptada'] === 1;
                        $esDocente = (int) $respuesta['autor_id'] === (int) $hilo['creador_id'];
                        ?>
                        <article class="forum-answer card<?= $aceptada ? ' forum-answer--accepted' : '' ?><?= $esDocente ? ' forum-answer--teacher' : '' ?>">
                            <header class="forum-answer__head">
                                <div class="forum-answer__who">
                                    <strong><?= htmlspecialchars($respuesta['autor']) ?></strong>
                                    <span class="forum-answer__role"> <?= $esDocente ? 'docente' : 'alumno' ?></span>
                                </div>
                                <div class="forum-answer__badges">
                                    <?php if ($aceptada) : ?>
                                        <span class="forum-badge forum-badge--accepted">Aceptada</span>
                                    <?php endif; ?>
                                    <?php if ($esDocente) : ?>
                                        <span class="forum-badge forum-badge--teacher">Docente</span>
                                    <?php endif; ?>
                                </div>
                            </header>
                            <p class="forum-answer__body"><?= htmlspecialchars($respuesta['cuerpo']) ?></p>
                            <footer class="forum-answer__foot"><?= fechaCorta($respuesta['creada_en']) ?></footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="forum-reply card" aria-labelledby="reply-heading">
                <h2 id="reply-heading" class="forum-reply__title">Tu respuesta</h2>

                <?php if ($hilo['estado'] === 'cerrada') : ?>
                    <p class="auth-hint">El hilo está cerrado: ya no se pueden publicar respuestas.</p>
                <?php elseif (!$logueado) : ?>
                    <p class="auth-hint">Necesitás <a class="auth-link" href="login.php">iniciar sesión</a> para responder.</p>
                <?php else : ?>
                    <p class="auth-hint">No podés responder dos veces seguidas: editá tu último mensaje o esperá a otro participante.</p>
                    <?php if ($error !== '') : ?>
                        <p class="auth-hint is-error" role="alert"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                    <form id="reply-form" class="forum-reply-form" action="foro-hilo.php?curso=<?= (int) $hilo['curso_id'] ?>&amp;id=<?= (int) $hilo['id'] ?>" method="post">
                        <label class="auth-label" for="reply-body">Escribí tu respuesta</label>
                        <textarea id="reply-body" name="body" class="auth-input auth-textarea" rows="4" placeholder="Aportá una solución clara…" required></textarea>
                        <button type="submit" class="auth-submit">
                            <span class="auth-submit__text">Publicar respuesta</span>
                            <span class="auth-submit__shine" aria-hidden="true"></span>
                        </button>
                    </form>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
