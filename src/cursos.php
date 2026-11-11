<?php
session_start();
require_once __DIR__ . '/../includes/conn.php';

$base = '..';
$navPage = 'cursos';

$busqueda = trim($_GET['q'] ?? '');
$precio = $_GET['precio'] ?? 'todos';
$acceso = $_GET['acceso'] ?? 'todos';
$etiqueta = $_GET['etiqueta'] ?? 'todos';
$soloDestacados = isset($_GET['destacados']);

$condiciones = [];
$parametros = [];

if ($busqueda !== '') {
    $condiciones[] = "(c.titulo LIKE ? OR c.descripcion LIKE ?
        OR EXISTS (
            SELECT 1 FROM curso_etiquetas ce
            JOIN etiquetas e ON e.id = ce.etiqueta_id
            WHERE ce.curso_id = c.id AND e.nombre LIKE ?
        ))";
    $parametros[] = '%' . $busqueda . '%';
    $parametros[] = '%' . $busqueda . '%';
    $parametros[] = '%' . $busqueda . '%';
}

if ($precio === 'gratis') {
    $condiciones[] = 'c.costo = 0';
} elseif ($precio === 'pago') {
    $condiciones[] = 'c.costo > 0';
}

if ($acceso === 'publico' || $acceso === 'privado') {
    $condiciones[] = 'c.acceso = ?';
    $parametros[] = $acceso;
}

if ($etiqueta !== '' && $etiqueta !== 'todos') {
    $condiciones[] = "EXISTS (
        SELECT 1 FROM curso_etiquetas ce
        JOIN etiquetas e ON e.id = ce.etiqueta_id
        WHERE ce.curso_id = c.id AND e.nombre = ?
    )";
    $parametros[] = $etiqueta;
}

if ($soloDestacados) {
    $condiciones[] = "u.plan = 'pro'";
}

$sql = "SELECT c.id, c.titulo, c.descripcion, c.costo, c.acceso, c.cupo,
               u.nombre, u.apellido, u.plan,
               (SELECT COUNT(*) FROM inscripciones i
                WHERE i.curso_id = c.id AND i.estado = 'confirmada') AS inscriptos
        FROM cursos c
        JOIN usuarios u ON u.id = c.creador_id";

if ($condiciones) {
    $sql .= ' WHERE ' . implode(' AND ', $condiciones);
}

$sql .= " ORDER BY (u.plan = 'pro') DESC, c.titulo";

$stmt = $conn->prepare($sql);
$stmt->execute($parametros);
$listaCursos = $stmt->fetchAll();

$etiquetasPorCurso = [];
$ids = array_column($listaCursos, 'id');
if ($ids) {
    $marcas = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT ce.curso_id, e.nombre
        FROM curso_etiquetas ce
        JOIN etiquetas e ON e.id = ce.etiqueta_id
        WHERE ce.curso_id IN ($marcas)
        ORDER BY e.nombre");
    $stmt->execute($ids);
    foreach ($stmt as $fila) {
        $etiquetasPorCurso[$fila['curso_id']][] = $fila['nombre'];
    }
}

$etiquetas = $conn->query('SELECT nombre FROM etiquetas ORDER BY nombre')->fetchAll();
$totalCursos = (int) $conn->query('SELECT COUNT(*) FROM cursos')->fetchColumn();

$filtrosBase = $_GET;
unset($filtrosBase['etiqueta']);
$urlTodas = 'cursos.php' . ($filtrosBase ? '?' . http_build_query($filtrosBase) : '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Cursos â€” Cursolandia</title>
</head>
<body class="page-cursos">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell">
        <header class="section-header page-cursos-header">
            <p class="section-eyebrow">CatÃ¡logo</p>
            <h1 class="section-title">Explorar cursos</h1>
            <p class="section-lead">
                BuscÃ¡ por nombre o etiqueta y combinÃ¡ filtros de precio, acceso y cursos destacados del plan Pro.
            </p>
        </header>

        <form class="glass-panel toolbar" role="search" method="get" action="cursos.php">
            <label class="courses-search" for="course-search">
                <span class="courses-search__label">Buscar</span>
                <input
                    type="search"
                    id="course-search"
                    name="q"
                    class="courses-search__input field-control"
                    placeholder="Nombre del curso o etiqueta (ej. php, foro)â€¦"
                    autocomplete="off"
                    value="<?= htmlspecialchars($busqueda) ?>"
                />
            </label>

            <div class="courses-filters">
                <div class="courses-filters__row">
                    <label class="filter-field" for="filter-price">
                        <span class="filter-field__label">Precio</span>
                        <select id="filter-price" name="precio" class="filter-select field-control field-control--select">
                            <option value="todos"<?= $precio === 'todos' ? ' selected' : '' ?>>Todos</option>
                            <option value="gratis"<?= $precio === 'gratis' ? ' selected' : '' ?>>Gratis</option>
                            <option value="pago"<?= $precio === 'pago' ? ' selected' : '' ?>>Pago</option>
                        </select>
                    </label>
                    <label class="filter-field" for="filter-access">
                        <span class="filter-field__label">Acceso</span>
                        <select id="filter-access" name="acceso" class="filter-select field-control field-control--select">
                            <option value="todos"<?= $acceso === 'todos' ? ' selected' : '' ?>>Todos</option>
                            <option value="publico"<?= $acceso === 'publico' ? ' selected' : '' ?>>PÃºblico</option>
                            <option value="privado"<?= $acceso === 'privado' ? ' selected' : '' ?>>Privado</option>
                        </select>
                    </label>
                    <label class="filter-field filter-field--check" for="filter-featured">
                        <input type="checkbox" id="filter-featured" name="destacados" value="1" class="filter-check"<?= $soloDestacados ? ' checked' : '' ?>/>
                        <span>Solo destacados Pro</span>
                    </label>
                    <button type="submit" class="filters-apply">Filtrar</button>
                    <a href="cursos.php" class="filters-reset">Limpiar filtros</a>
                </div>
                <div class="courses-filters__tags">
                    <span class="filter-field__label">Etiquetas</span>
                    <div id="filter-tags" class="filter-chips" role="group" aria-label="Filtrar por etiqueta">
                        <a href="<?= htmlspecialchars($urlTodas) ?>" class="filter-chip<?= $etiqueta === 'todos' || $etiqueta === '' ? ' is-active' : '' ?>">Todas las etiquetas</a>
                        <?php foreach ($etiquetas as $tag) : ?>
                            <?php
                            $paramsTag = $filtrosBase;
                            $paramsTag['etiqueta'] = $tag['nombre'];
                            ?>
                            <a href="cursos.php?<?= htmlspecialchars(http_build_query($paramsTag)) ?>" class="filter-chip<?= $etiqueta === $tag['nombre'] ? ' is-active' : '' ?>">
                                <?= htmlspecialchars($tag['nombre']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </form>

        <p id="courses-count" class="results-count courses-results-count" aria-live="polite">
            <?php if (count($listaCursos) === $totalCursos) : ?>
                <?= count($listaCursos) ?> curso<?= count($listaCursos) === 1 ? '' : 's' ?>
            <?php else : ?>
                <?= count($listaCursos) ?> de <?= $totalCursos ?> cursos
            <?php endif; ?>
        </p>

        <div id="cursos-grid" class="card-grid card-grid--catalog" aria-live="polite">
            <?php if (!$listaCursos) : ?>
                <p class="empty-state empty-state--grid">No hay cursos con esos criterios. ProbÃ¡ otro nombre o quitÃ¡ algÃºn filtro.</p>
            <?php endif; ?>

            <?php foreach ($listaCursos as $curso) : ?>
                <?php
                $destacado = $curso['plan'] === 'pro';
                $esPago = $curso['costo'] > 0;
                $tagsCurso = $etiquetasPorCurso[$curso['id']] ?? [];
                $docente = $curso['nombre'] . ' ' . $curso['apellido'];
                ?>
                <article class="card course-card<?= $destacado ? ' card-accent-pro' : '' ?>">
                    <div class="course-card__badges">
                        <?php if ($destacado) : ?>
                            <span class="course-badge course-badge--pro">Destacado Pro</span>
                        <?php endif; ?>
                        <span class="course-badge course-badge--<?= $esPago ? 'pago' : 'gratis' ?>"><?= $esPago ? 'Pago' : 'Gratis' ?></span>
                    </div>
                    <h2 class="card-title course-card__title"><?= htmlspecialchars($curso['titulo']) ?></h2>
                    <p class="card-text"><?= htmlspecialchars($curso['descripcion']) ?></p>
                    <p class="course-card__meta"><?= $curso['acceso'] === 'publico' ? 'PÃºblico' : 'Privado' ?> Â· <?= htmlspecialchars($docente) ?></p>
                    <ul class="course-card__tags">
                        <?php foreach ($tagsCurso as $tag) : ?>
                            <li><?= htmlspecialchars($tag) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="curso.php?id=<?= (int) $curso['id'] ?>" class="btn-outline btn-outline--cyan course-card__cta">Ver curso</a>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

