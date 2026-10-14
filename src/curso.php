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
    <title>Curso — Cursolandia</title>
</head>
<body class="page-curso">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell page-shell--compact" id="curso-app">
        <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta del curso">
            <a class="text-link" href="cursos.php">Cursos</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <span id="curso-breadcrumb-title">Cargando…</span>
        </nav>

        <header class="curso-hero" id="curso-hero" hidden>
            <div class="curso-hero__glow" aria-hidden="true"></div>
            <div class="curso-hero__inner card glass-panel">
                <div id="curso-hero-badges" class="course-card__badges"></div>
                <h1 id="curso-title" class="curso-hero__title"></h1>
                <p id="curso-description" class="curso-hero__desc"></p>
                <ul id="curso-tags" class="course-card__tags"></ul>
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
                        <p id="curso-info-body" class="curso-info__body"></p>
                    </div>
                    <div class="curso-stats card">
                        <h2 class="curso-info__title">Detalles de vigencia y acceso</h2>
                        <dl class="curso-dl" id="curso-detalles"></dl>
                    </div>
                    <div class="curso-docente card">
                        <h2 class="curso-info__title">Docente</h2>
                        <div class="curso-docente__row">
                            <div class="curso-docente__avatar" id="curso-docente-avatar" aria-hidden="true"></div>
                            <div>
                                <p class="curso-docente__name" id="curso-docente-name"></p>
                                <p class="curso-docente__hint">Creador del curso y moderador del foro Q&amp;A.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="curso-panel" id="panel-material" role="tabpanel" aria-labelledby="tab-material" hidden>
                    <div class="curso-material card">
                        <h2 class="curso-info__title">Archivos del curso</h2>
                        <p class="auth-hint">Material subido por el docente. En plan gratuito aplican límites de cantidad y tamaño por archivo.</p>
                        <ul id="curso-materiales" class="curso-material-list"></ul>
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
                    <ul id="forum-thread-list" class="forum-thread-list" aria-live="polite"></ul>
                </section>
            </div>

            <aside class="curso-sidebar card" id="curso-sidebar" hidden>
                <p class="curso-sidebar__price" id="curso-sidebar-price"></p>
                <p class="curso-sidebar__access" id="curso-sidebar-access"></p>
                <button type="button" class="auth-submit curso-enroll-btn" id="curso-enroll-btn" disabled title="Próximamente con sesión iniciada">
                    <span class="auth-submit__text" id="curso-enroll-label">Inscribirme</span>
                    <span class="auth-submit__shine" aria-hidden="true"></span>
                </button>
                <p class="curso-sidebar__note" id="curso-sidebar-note"></p>
                <ul class="curso-sidebar__stats" id="curso-sidebar-stats"></ul>
                <button type="button" class="btn-outline btn-outline--green curso-sidebar__foro-link" id="curso-jump-foro">
                    Ir al foro del curso
                </button>
            </aside>
        </div>

        <p id="curso-error" class="empty-state empty-state--compact" hidden></p>
    </main>

    <script src="../scripts/curso.js" defer></script>
    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
