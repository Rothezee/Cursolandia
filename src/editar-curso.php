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
$id = (int) ($_GET['id'] ?? 0);
$error = '';
$limite = 3;

// Solo el creador puede ver y editar el curso
$stmt = $conn->prepare('SELECT * FROM cursos WHERE id = ? AND creador_id = ?');
$stmt->execute([$id, $usuarioId]);
$curso = $stmt->fetch();

if ($curso) {
    $plan = $conn->query("SELECT plan FROM usuarios WHERE id = $usuarioId")->fetchColumn();
    $esPro = $plan === 'pro';
    $hoy = date('Y-m-d');

    $stmt = $conn->prepare('SELECT COUNT(*) FROM inscripciones WHERE curso_id = ?');
    $stmt->execute([$id]);
    $totalInscripciones = (int) $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT COUNT(*) FROM inscripciones WHERE curso_id = ? AND estado = 'confirmada'");
    $stmt->execute([$id]);
    $confirmados = (int) $stmt->fetchColumn();

    $stmt = $conn->prepare('SELECT COUNT(*) FROM cursos
        WHERE creador_id = ? AND id <> ? AND fecha_fin >= CURDATE()');
    $stmt->execute([$usuarioId, $id]);
    $activosOtros = (int) $stmt->fetchColumn();

    // Valores iniciales: los datos actuales del curso
    $stmt = $conn->prepare('SELECT e.nombre
        FROM curso_etiquetas ce
        JOIN etiquetas e ON e.id = ce.etiqueta_id
        WHERE ce.curso_id = ?
        ORDER BY e.nombre');
    $stmt->execute([$id]);
    $etiquetasActuales = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $titulo = $curso['titulo'];
    $descripcion = $curso['descripcion'];
    $etiquetasTexto = implode(', ', $etiquetasActuales);
    $costo = (float) $curso['costo'] > 0 ? (string) $curso['costo'] : '';
    $cupo = $curso['cupo'] === null ? '' : (string) $curso['cupo'];
    $acceso = $curso['acceso'];
    $fechaInicio = $curso['fecha_inicio'];
    $fechaFin = $curso['fecha_fin'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? 'guardar';

        if ($accion === 'guardar') {
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
            } elseif ($cupo !== '' && (int) $cupo < $confirmados) {
                $error = 'El cupo no puede ser menor a los inscriptos confirmados (' . $confirmados . ').';
            } elseif ($acceso !== 'publico' && $acceso !== 'privado') {
                $error = 'Elegí un tipo de acceso válido.';
            } elseif (!$esPro && $fechaFin >= $hoy && $activosOtros >= $limite) {
                $error = 'El plan gratuito permite hasta ' . $limite . ' cursos activos. Con esas fechas este curso quedaría activo y superarías el límite. Solicitá el plan Pro o poné una fecha de fin pasada.';
            } else {
                try {
                    $conn->beginTransaction();

                    $stmt = $conn->prepare('UPDATE cursos
                        SET titulo = ?, descripcion = ?, costo = ?, acceso = ?, cupo = ?, fecha_inicio = ?, fecha_fin = ?
                        WHERE id = ? AND creador_id = ?');
                    $stmt->execute([
                        $titulo,
                        $descripcion,
                        $costo === '' ? 0 : (float) $costo,
                        $acceso,
                        $cupo === '' ? null : (int) $cupo,
                        $fechaInicio,
                        $fechaFin,
                        $id,
                        $usuarioId,
                    ]);

                    // Etiquetas: se reemplazan por las que vienen del formulario
                    $stmt = $conn->prepare('DELETE FROM curso_etiquetas WHERE curso_id = ?');
                    $stmt->execute([$id]);

                    $nuevas = [];
                    foreach (explode(',', $etiquetasTexto) as $etiqueta) {
                        $etiqueta = strtolower(trim($etiqueta));
                        if ($etiqueta !== '' && strlen($etiqueta) <= 50) {
                            $nuevas[$etiqueta] = true;
                        }
                    }

                    foreach (array_keys($nuevas) as $etiqueta) {
                        $stmt = $conn->prepare('SELECT id FROM etiquetas WHERE nombre = ?');
                        $stmt->execute([$etiqueta]);
                        $etiquetaId = $stmt->fetchColumn();

                        if (!$etiquetaId) {
                            $stmt = $conn->prepare('INSERT INTO etiquetas (nombre) VALUES (?)');
                            $stmt->execute([$etiqueta]);
                            $etiquetaId = $conn->lastInsertId();
                        }

                        $stmt = $conn->prepare('INSERT INTO curso_etiquetas (curso_id, etiqueta_id) VALUES (?, ?)');
                        $stmt->execute([$id, $etiquetaId]);
                    }

                    $conn->commit();
                    header('Location: curso.php?id=' . $id . '&ok=editado');
                    exit;
                } catch (PDOException $e) {
                    if ($conn->inTransaction()) {
                        $conn->rollBack();
                    }
                    $error = 'No se pudo guardar el curso. Probá de nuevo.';
                }
            }
        }

        if ($accion === 'eliminar') {
            if ($totalInscripciones > 0) {
                $error = 'No se puede eliminar el curso porque tiene '
                    . $totalInscripciones . ' inscripción' . ($totalInscripciones === 1 ? '' : 'es')
                    . '. Rechazá o resolvé las inscripciones antes de intentarlo.';
            } else {
                try {
                    $stmt = $conn->prepare('SELECT ruta FROM materiales WHERE curso_id = ?');
                    $stmt->execute([$id]);
                    $rutas = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    $conn->beginTransaction();
                    $stmt = $conn->prepare('DELETE FROM respuestas
                        WHERE pregunta_id IN (SELECT id FROM preguntas WHERE curso_id = ?)');
                    $stmt->execute([$id]);
                    $stmt = $conn->prepare('DELETE FROM preguntas WHERE curso_id = ?');
                    $stmt->execute([$id]);
                    $stmt = $conn->prepare('DELETE FROM materiales WHERE curso_id = ?');
                    $stmt->execute([$id]);
                    $stmt = $conn->prepare('DELETE FROM curso_etiquetas WHERE curso_id = ?');
                    $stmt->execute([$id]);
                    $stmt = $conn->prepare('DELETE FROM cursos WHERE id = ? AND creador_id = ?');
                    $stmt->execute([$id, $usuarioId]);
                    $conn->commit();

                    // Borrar archivos del disco (solo dentro de uploads/cursos/)
                    foreach ($rutas as $ruta) {
                        if (strpos($ruta, 'uploads/cursos/') === 0 && strpos($ruta, '..') === false) {
                            @unlink(__DIR__ . '/../' . $ruta);
                        }
                    }
                    @rmdir(__DIR__ . '/../uploads/cursos/' . $id);

                    header('Location: mis-cursos.php?eliminado=1');
                    exit;
                } catch (PDOException $e) {
                    if ($conn->inTransaction()) {
                        $conn->rollBack();
                    }
                    $error = 'No se pudo eliminar el curso. Probá de nuevo.';
                }
            }
        }
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
    <title>Editar curso — Cursolandia</title>
</head>
<body class="page-cursos">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell page-shell--compact">
        <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta">
            <a class="text-link" href="mis-cursos.php">Mis cursos</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <span>Editar curso</span>
        </nav>

        <?php if (!$curso) : ?>
            <p class="empty-state empty-state--compact">
                No encontramos ese curso o no tenés permiso para editarlo.
                <a class="text-link" href="mis-cursos.php">Volver a Mis cursos</a>
            </p>
        <?php else : ?>
            <header class="section-header page-cursos-header">
                <p class="section-eyebrow">Docente</p>
                <h1 class="section-title">Editar curso</h1>
                <p class="section-lead">
                    Modificá los datos del curso. El título, la descripción y las fechas son obligatorios;
                    las etiquetas, el costo y el cupo son opcionales.
                </p>
            </header>

            <section class="card glass-panel">
                <?php if ($error !== '') : ?>
                    <p class="auth-hint is-error" role="alert"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <?php if (!$esPro) : ?>
                    <p class="auth-hint">
                        Plan gratuito: <?= $activosOtros + ($curso['fecha_fin'] >= $hoy ? 1 : 0) ?> de <?= $limite ?> cursos activos.
                    </p>
                <?php endif; ?>

                <form class="auth-form" method="post" action="editar-curso.php?id=<?= (int) $curso['id'] ?>" novalidate>
                    <input type="hidden" name="accion" value="guardar"/>

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
                        <span class="auth-submit__text">Guardar cambios</span>
                        <span class="auth-submit__shine" aria-hidden="true"></span>
                    </button>
                </form>
            </section>

            <section class="card glass-panel">
                <h2 class="curso-info__title">Eliminar curso</h2>
                <?php if ($totalInscripciones > 0) : ?>
                    <p class="auth-hint">
                        Este curso tiene <?= $totalInscripciones ?> inscripción<?= $totalInscripciones === 1 ? '' : 'es' ?>,
                        por eso no se puede eliminar.
                    </p>
                    <button type="button" class="forum-action-btn forum-action-btn--danger" disabled>Eliminar curso</button>
                <?php else : ?>
                    <p class="auth-hint">
                        Se borrarán también el material, las preguntas del foro y las etiquetas del curso. Esta acción no se puede deshacer.
                    </p>
                    <form method="post" action="editar-curso.php?id=<?= (int) $curso['id'] ?>"
                          onsubmit="return confirm('¿Seguro que querés eliminar este curso? No se puede deshacer.');">
                        <input type="hidden" name="accion" value="eliminar"/>
                        <button type="submit" class="forum-action-btn forum-action-btn--danger">Eliminar curso</button>
                    </form>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>