<?php
$base = '..';
$navPage = 'cursos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Hilo — Foro del curso</title>
</head>
<body class="page-foro page-foro-hilo">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell" id="forum-hilo-root">
        <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta del hilo">
            <a class="text-link" href="cursos.php">Cursos</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <a id="breadcrumb-curso" class="text-link" href="curso.php">Curso</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <a id="breadcrumb-foro" class="text-link" href="curso.php">Foro</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <span>Hilo</span>
        </nav>

        <article class="forum-question card">
            <div class="forum-thread__head">
                <div class="forum-thread__badges">
                    <span id="hilo-status" class="forum-badge forum-badge--abierta">Abierta</span>
                </div>
            </div>
            <h1 id="hilo-title" class="forum-question__title"></h1>
            <p id="hilo-excerpt" class="forum-question__body"></p>
            <footer class="forum-question__meta meta-row">
                <span>Pregunta de <strong id="hilo-author"></strong></span>
                <span id="hilo-date"></span>
            </footer>
            <div class="forum-question__mod-actions">
                <button type="button" class="forum-action-btn" disabled title="Docente: próximamente">Destacar</button>
                <button type="button" class="forum-action-btn forum-action-btn--danger" disabled title="Docente: próximamente">Eliminar pregunta</button>
            </div>
        </article>

        <section class="forum-answers-section" aria-labelledby="answers-heading">
            <h2 id="answers-heading" class="forum-answers-heading">
                Respuestas <span class="forum-answers-count">(<span id="answers-count">0</span>)</span>
            </h2>
            <div id="forum-answers" class="forum-answers"></div>
        </section>

        <section class="forum-reply card" aria-labelledby="reply-heading">
            <h2 id="reply-heading" class="forum-reply__title">Tu respuesta</h2>
            <p class="auth-hint">No podés responder dos veces seguidas: editá tu último mensaje o esperá a otro participante.</p>
            <form id="reply-form" class="forum-reply-form" action="#" method="post">
                <label class="auth-label" for="reply-body">Escribí tu respuesta</label>
                <textarea id="reply-body" name="body" class="auth-input auth-textarea" rows="4" placeholder="Aportá una solución clara…" required></textarea>
                <button type="submit" class="auth-submit">
                    <span class="auth-submit__text">Publicar respuesta</span>
                    <span class="auth-submit__shine" aria-hidden="true"></span>
                </button>
            </form>
        </section>
    </main>

    <script src="../scripts/foro-hilo.js" defer></script>
    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
