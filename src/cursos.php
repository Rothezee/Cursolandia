<?php
session_start();
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
    <title>Cursos — Cursolandia</title>
</head>
<body class="page-cursos">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell">
        <header class="section-header page-cursos-header">
            <p class="section-eyebrow">Catálogo</p>
            <h1 class="section-title">Explorar cursos</h1>
            <p class="section-lead">
                Buscá por nombre o etiqueta y combiná filtros de precio, acceso y cursos destacados del plan Pro.
            </p>
        </header>

        <div class="glass-panel toolbar" role="search">
            <label class="courses-search" for="course-search">
                <span class="courses-search__label">Buscar</span>
                <input
                    type="search"
                    id="course-search"
                    class="courses-search__input field-control"
                    placeholder="Nombre del curso o etiqueta (ej. php, foro)…"
                    autocomplete="off"
                />
            </label>

            <div class="courses-filters">
                <div class="courses-filters__row">
                    <label class="filter-field" for="filter-price">
                        <span class="filter-field__label">Precio</span>
                        <select id="filter-price" class="filter-select field-control field-control--select">
                            <option value="todos">Todos</option>
                            <option value="gratis">Gratis</option>
                            <option value="pago">Pago</option>
                        </select>
                    </label>
                    <label class="filter-field" for="filter-access">
                        <span class="filter-field__label">Acceso</span>
                        <select id="filter-access" class="filter-select field-control field-control--select">
                            <option value="todos">Todos</option>
                            <option value="publico">Público</option>
                            <option value="privado">Privado</option>
                        </select>
                    </label>
                    <label class="filter-field filter-field--check" for="filter-featured">
                        <input type="checkbox" id="filter-featured" class="filter-check"/>
                        <span>Solo destacados Pro</span>
                    </label>
                    <button type="button" id="filters-reset" class="filters-reset">Limpiar filtros</button>
                </div>
                <div class="courses-filters__tags">
                    <span class="filter-field__label">Etiquetas</span>
                    <div id="filter-tags" class="filter-chips" role="group" aria-label="Filtrar por etiqueta"></div>
                </div>
            </div>
        </div>

        <p id="courses-count" class="results-count courses-results-count" aria-live="polite"></p>

        <div id="cursos-grid" class="card-grid card-grid--catalog" aria-live="polite"></div>
    </main>

    <script src="../scripts/cursos-catalog.js" defer></script>
    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
