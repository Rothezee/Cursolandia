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
$errorPerfil = '';
$errorPass = '';
$form = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'editar_perfil') {
        $form = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'usuario' => trim($_POST['usuario'] ?? ''),
            'intereses' => trim($_POST['intereses'] ?? ''),
            'antecedentes' => trim($_POST['antecedentes'] ?? ''),
        ];

        if ($form['nombre'] === '' || $form['apellido'] === '' || $form['email'] === '' || $form['usuario'] === '') {
            $errorPerfil = 'Nombre, apellido, correo y usuario son obligatorios.';
        } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
            $errorPerfil = 'Ingresá un correo electrónico válido.';
        } else {
            try {
                $stmt = $conn->prepare('UPDATE usuarios
                    SET nombre = ?, apellido = ?, email = ?, telefono = ?, usuario = ?, intereses = ?, antecedentes = ?
                    WHERE id = ?');
                $stmt->execute([
                    $form['nombre'],
                    $form['apellido'],
                    $form['email'],
                    $form['telefono'] !== '' ? $form['telefono'] : null,
                    $form['usuario'],
                    $form['intereses'] !== '' ? $form['intereses'] : null,
                    $form['antecedentes'] !== '' ? $form['antecedentes'] : null,
                    $usuarioId,
                ]);
                $_SESSION['usuario'] = $form['usuario'];
                header('Location: perfil.php?ok=perfil');
                exit;
            } catch (PDOException $e) {
                if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                    $errorPerfil = 'Ese correo o nombre de usuario ya está registrado.';
                } else {
                    $errorPerfil = 'No se pudo guardar el perfil. Probá de nuevo.';
                }
            }
        }
    }

    if ($accion === 'cambiar_password') {
        $actual = $_POST['password_actual'] ?? '';
        $nueva = $_POST['password_nueva'] ?? '';
        $confirmar = $_POST['password_confirm'] ?? '';

        $stmt = $conn->prepare('SELECT password FROM usuarios WHERE id = ?');
        $stmt->execute([$usuarioId]);
        $hashActual = $stmt->fetchColumn();

        if ($actual === '' || $nueva === '' || $confirmar === '') {
            $errorPass = 'Completá los tres campos.';
        } elseif (!$hashActual || !password_verify($actual, $hashActual)) {
            $errorPass = 'La contraseña actual es incorrecta.';
        } elseif (strlen($nueva) < 8) {
            $errorPass = 'La nueva contraseña debe tener al menos 8 caracteres.';
        } elseif ($nueva !== $confirmar) {
            $errorPass = 'Las contraseñas nuevas no coinciden.';
        } elseif ($nueva === $actual) {
            $errorPass = 'La nueva contraseña debe ser distinta de la actual.';
        } else {
            $stmt = $conn->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
            $stmt->execute([password_hash($nueva, PASSWORD_DEFAULT), $usuarioId]);
            session_regenerate_id(true);
            header('Location: perfil.php?ok=password');
            exit;
        }
    }
}

$stmt = $conn->prepare('SELECT nombre, apellido, email, telefono, usuario, intereses, antecedentes,
        plan, plan_vencimiento, rol
    FROM usuarios WHERE id = ?');
$stmt->execute([$usuarioId]);
$usuario = $stmt->fetch();
$esPro = $usuario['plan'] === 'pro';
$esAdmin = $usuario['rol'] === 'admin';

if ($form === null) {
    $form = [
        'nombre' => $usuario['nombre'],
        'apellido' => $usuario['apellido'],
        'email' => $usuario['email'],
        'telefono' => $usuario['telefono'] ?? '',
        'usuario' => $usuario['usuario'],
        'intereses' => $usuario['intereses'] ?? '',
        'antecedentes' => $usuario['antecedentes'] ?? '',
    ];
}

$mensajesOk = [
    'perfil' => 'Perfil actualizado correctamente.',
    'password' => 'Contraseña actualizada correctamente.',
];
$ok = $_GET['ok'] ?? '';

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

            <?php if (isset($mensajesOk[$ok])) : ?>
                <p class="auth-hint is-ok" role="status"><?= $mensajesOk[$ok] ?></p>
            <?php endif; ?>

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

            <details class="card forum-new-question"<?= $errorPerfil !== '' ? ' open' : '' ?>>
                <summary class="forum-new-btn">Editar perfil</summary>

                <?php if ($errorPerfil !== '') : ?>
                    <p class="auth-hint is-error" role="alert"><?= htmlspecialchars($errorPerfil) ?></p>
                <?php endif; ?>

                <form class="auth-form" method="post" action="perfil.php" novalidate>
                    <input type="hidden" name="accion" value="editar_perfil"/>

                    <div class="auth-grid">
                        <div class="auth-field">
                            <label class="auth-label" for="perfil-nombre">Nombre</label>
                            <input type="text" id="perfil-nombre" name="nombre" class="auth-input"
                                   value="<?= htmlspecialchars($form['nombre']) ?>" autocomplete="given-name" required />
                        </div>
                        <div class="auth-field">
                            <label class="auth-label" for="perfil-apellido">Apellido</label>
                            <input type="text" id="perfil-apellido" name="apellido" class="auth-input"
                                   value="<?= htmlspecialchars($form['apellido']) ?>" autocomplete="family-name" required />
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="perfil-email">Correo electrónico</label>
                        <input type="email" id="perfil-email" name="email" class="auth-input"
                               value="<?= htmlspecialchars($form['email']) ?>" autocomplete="email" required />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="perfil-telefono">Teléfono <span class="auth-optional">(opcional)</span></label>
                        <input type="tel" id="perfil-telefono" name="telefono" class="auth-input"
                               value="<?= htmlspecialchars($form['telefono']) ?>" autocomplete="tel" />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="perfil-usuario">Nombre de usuario público</label>
                        <input type="text" id="perfil-usuario" name="usuario" class="auth-input"
                               value="<?= htmlspecialchars($form['usuario']) ?>" autocomplete="username" required />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="perfil-intereses">Intereses <span class="auth-optional">(opcional, separados por comas)</span></label>
                        <input type="text" id="perfil-intereses" name="intereses" class="auth-input"
                               value="<?= htmlspecialchars($form['intereses']) ?>" placeholder="php, diseño web, bases de datos…" />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="perfil-antecedentes">Antecedentes <span class="auth-optional">(opcional)</span></label>
                        <textarea id="perfil-antecedentes" name="antecedentes" class="auth-input auth-textarea"
                                  rows="3"><?= htmlspecialchars($form['antecedentes']) ?></textarea>
                    </div>

                    <button type="submit" class="auth-submit">
                        <span class="auth-submit__text">Guardar cambios</span>
                        <span class="auth-submit__shine" aria-hidden="true"></span>
                    </button>
                </form>
            </details>

            <details class="card forum-new-question"<?= $errorPass !== '' ? ' open' : '' ?>>
                <summary class="forum-new-btn">Cambiar contraseña</summary>

                <?php if ($errorPass !== '') : ?>
                    <p class="auth-hint is-error" role="alert"><?= htmlspecialchars($errorPass) ?></p>
                <?php endif; ?>

                <form class="auth-form" method="post" action="perfil.php" novalidate>
                    <input type="hidden" name="accion" value="cambiar_password"/>

                    <div class="auth-field">
                        <label class="auth-label" for="pass-actual">Contraseña actual</label>
                        <input type="password" id="pass-actual" name="password_actual" class="auth-input"
                               autocomplete="current-password" required />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="pass-nueva">Contraseña nueva</label>
                        <input type="password" id="pass-nueva" name="password_nueva" class="auth-input"
                               autocomplete="new-password" minlength="8" required />
                        <p class="auth-hint">Mínimo 8 caracteres.</p>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="pass-confirm">Confirmar contraseña nueva</label>
                        <input type="password" id="pass-confirm" name="password_confirm" class="auth-input"
                               autocomplete="new-password" minlength="8" required />
                    </div>

                    <button type="submit" class="auth-submit">
                        <span class="auth-submit__text">Cambiar contraseña</span>
                        <span class="auth-submit__shine" aria-hidden="true"></span>
                    </button>
                </form>
            </details>

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