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
$error = '';

$plan = $conn->query("SELECT plan FROM usuarios WHERE id = $usuarioId")->fetchColumn();
$esPro = $plan === 'pro';

$activos = $conn->query("SELECT COUNT(*) FROM cursos
    WHERE creador_id = $usuarioId AND fecha_fin >= CURDATE()")->fetchColumn();
$limite = 3;

$titulo = '';
$descripcion = '';
$etiquetasTexto = '';
$costo = '';
$cupo = '';
$acceso = 'publico';
$fechaInicio = '';
$fechaFin = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $etiquetasTexto = trim($_POST['etiquetas'] ?? '');
    $costo = trim($_POST['costo'] ?? '');
    $cupo = trim($_POST['cupo'] ?? '');
    $acceso = $_POST['acceso'] ?? 'publico';
    $fechaInicio = $_POST['fecha_inicio'] ?? '';
    $fechaFin = $_POST['fecha_fin'] ?? '';

    if ($titulo === '' || $descripcion === '') {
        $error = 'El título y la descripción son obligatorios.';
    } elseif (strlen($titulo) > 150) {
        $error = 'El título no puede superar los 150 caracteres.';
    } elseif ($fechaInicio === '' || $fechaFin === '') {
        $error = 'Indicá una fecha de inicio y una fecha de fin.';
    } elseif ($fechaFin < $fechaInicio) {
        $error = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
    } elseif ($costo !== '' && (!is_numeric($costo) || $costo < 0)) {
        $error = 'El costo debe ser un número mayor o igual a 0.';
    } elseif ($cupo !== '' && (!ctype_digit($cupo) || (int) $cupo < 1)) {
        $error = 'El cupo debe ser un número entero mayor a 0.';
    } elseif ($acceso !== 'publico' && $acceso !== 'privado') {
        $error = 'Elegí un tipo de acceso válido.';
    } elseif (!$esPro && $activos >= $limite) {
        $error = 'El plan gratuito permite hasta ' . $limite . ' cursos activos. Para crear más, solicitá el plan Pro.';
    } else {
        $tituloSql = $conn->quote($titulo);
        $descripcionSql = $conn->quote($descripcion);
        $accesoSql = $conn->quote($acceso);
        $inicioSql = $conn->quote($fechaInicio);
        $finSql = $conn->quote($fechaFin);
        $costoSql = $costo === '' ? 0 : (float) $costo;
        $cupoSql = $cupo === '' ? 'NULL' : (int) $cupo;

        $conn->query("INSERT INTO cursos
            (creador_id, titulo, descripcion, costo, acceso, cupo, fecha_inicio, fecha_fin)
            VALUES ($usuarioId, $tituloSql, $descripcionSql, $costoSql, $accesoSql, $cupoSql, $inicioSql, $finSql)");
        $cursoId = (int) $conn->lastInsertId();

        foreach (explode(',', $etiquetasTexto) as $etiqueta) {
            $etiqueta = strtolower(trim($etiqueta));

            if ($etiqueta === '' || strlen($etiqueta) > 50) {
                continue;
            }

            $etiquetaSql = $conn->quote($etiqueta);
            $etiquetaId = $conn->query("SELECT id FROM etiquetas WHERE nombre = $etiquetaSql")->fetchColumn();

            if (!$etiquetaId) {
                $conn->query("INSERT INTO etiquetas (nombre) VALUES ($etiquetaSql)");
                $etiquetaId = $conn->lastInsertId();
            }

            $conn->query("INSERT INTO curso_etiquetas (curso_id, etiqueta_id) VALUES ($cursoId, $etiquetaId)");
        }

        header('Location: curso.php?id=' . $cursoId . '&creado=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Crear curso — Cursolandia</title>
</head>
<body class="page-cursos">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell page-shell--compact">
        <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta">
            <a class="text-link" href="mis-cursos.php">Mis cursos</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <span>Crear curso</span>
        </nav>

        <header class="section-header page-cursos-header">
            <p class="section-eyebrow">Nuevo curso</p>
            <h1 class="section-title">Crear curso</h1>
            <p class="section-lead">
                Completá los datos del curso. El título, la descripción y las fechas son obligatorios;
                las etiquetas, el costo y el cupo son opcionales.
            </p>
        </header>

        <section class="card glass-panel">
            <?php if ($error !== '') : ?>
                <p class="auth-hint is-error" role="alert"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if (!$esPro) : ?>
                <p class="auth-hint">
                    Plan gratuito: <?= $activos ?> de <?= $limite ?> cursos activos.
                </p>
            <?php endif; ?>

            <form class="auth-form" method="post" action="crear-curso.php" novalidate>
                <div class="auth-field">
                    <label class="auth-label" for="curso-titulo">Título</label>
                    <input
                        type="text"
                        id="curso-titulo"
                        name="titulo"
                        class="auth-input"
                        maxlength="150"
                        value="<?= htmlspecialchars($titulo) ?>"
                        required
                    />
                </div>

                <div class="auth-field">
                    <label class="auth-label" for="curso-descripcion">Descripción</label>
                    <textarea
                        id="curso-descripcion"
                        name="descripcion"
                        class="auth-input auth-textarea"
                        rows="4"
                        required
                    ><?= htmlspecialchars($descripcion) ?></textarea>
                </div>

                <div class="auth-field">
                    <label class="auth-label" for="curso-etiquetas">
                        Etiquetas <span class="auth-optional">(opcional, separadas por comas)</span>
                    </label>
                    <input
                        type="text"
                        id="curso-etiquetas"
                        name="etiquetas"
                        class="auth-input"
                        placeholder="php, backend, foro"
                        value="<?= htmlspecialchars($etiquetasTexto) ?>"
                    />
                </div>

                <div class="auth-grid">
                    <div class="auth-field">
                        <label class="auth-label" for="curso-costo">
                            Costo <span class="auth-optional">(opcional, 0 = gratis)</span>
                        </label>
                        <input
                            type="number"
                            id="curso-costo"
                            name="costo"
                            class="auth-input"
                            min="0"
                            step="0.01"
                            placeholder="0"
                            value="<?= htmlspecialchars($costo) ?>"
                        />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="curso-cupo">
                            Cupo <span class="auth-optional">(opcional, vacío = sin límite)</span>
                        </label>
                        <input
                            type="number"
                            id="curso-cupo"
                            name="cupo"
                            class="auth-input"
                            min="1"
                            step="1"
                            placeholder="Sin límite"
                            value="<?= htmlspecialchars($cupo) ?>"
                        />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="curso-inicio">Fecha de inicio</label>
                        <input
                            type="date"
                            id="curso-inicio"
                            name="fecha_inicio"
                            class="auth-input"
                            value="<?= htmlspecialchars($fechaInicio) ?>"
                            required
                        />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="curso-fin">Fecha de fin</label>
                        <input
                            type="date"
                            id="curso-fin"
                            name="fecha_fin"
                            class="auth-input"
                            value="<?= htmlspecialchars($fechaFin) ?>"
                            required
                        />
                    </div>
                </div>

                <div class="auth-field">
                    <label class="auth-label" for="curso-acceso">Acceso</label>
                    <select id="curso-acceso" name="acceso" class="auth-input filter-select">
                        <option value="publico"<?= $acceso === 'publico' ? ' selected' : '' ?>>Público</option>
                        <option value="privado"<?= $acceso === 'privado' ? ' selected' : '' ?>>Privado</option>
                    </select>
                </div>

                <button type="submit" class="auth-submit">
                    <span class="auth-submit__text">Publicar curso</span>
                    <span class="auth-submit__shine" aria-hidden="true"></span>
                </button>
            </form>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
