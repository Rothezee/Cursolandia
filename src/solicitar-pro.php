<?php
session_start();
require_once __DIR__ . '/../includes/conn.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$base = '..';
$navPage = 'planes';
$usuarioId = (int) $_SESSION['usuario_id'];
$error = '';

$usuario = $conn->query("SELECT plan, plan_vencimiento FROM usuarios WHERE id = $usuarioId")->fetch();
$esPro = $usuario['plan'] === 'pro';

$pendiente = $conn->query("SELECT COUNT(*) FROM solicitudes_pro
    WHERE usuario_id = $usuarioId AND estado = 'pendiente'")->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($esPro) {
        $error = 'Ya tenés el plan Pro activo.';
    } elseif ($pendiente > 0) {
        $error = 'Ya tenés una solicitud pendiente de revisión.';
    } elseif (empty($_FILES['comprobante']['name'])) {
        $error = 'Adjuntá el comprobante de pago.';
    } else {
        $archivo = $_FILES['comprobante'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($extension, $permitidas)) {
            $error = 'El comprobante debe ser JPG, PNG o PDF.';
        } elseif ($archivo['size'] > 5 * 1024 * 1024) {
            $error = 'El comprobante no puede superar los 5 MB.';
        } elseif ($archivo['error'] !== 0) {
            $error = 'No se pudo subir el archivo.';
        } else {
            $carpeta = __DIR__ . '/../uploads/comprobantes/';

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $nombre = 'comprobante-' . $usuarioId . '-' . time() . '.' . $extension;

            if (move_uploaded_file($archivo['tmp_name'], $carpeta . $nombre)) {
                $rutaSql = $conn->quote('uploads/comprobantes/' . $nombre);
                $conn->query("INSERT INTO solicitudes_pro (usuario_id, comprobante)
                    VALUES ($usuarioId, $rutaSql)");
                header('Location: solicitar-pro.php?ok=1');
                exit;
            }

            $error = 'No se pudo guardar el comprobante.';
        }
    }
}

$solicitudes = $conn->query("SELECT * FROM solicitudes_pro
    WHERE usuario_id = $usuarioId ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Solicitar plan Pro — Cursolandia</title>
</head>
<body class="page-planes">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell page-shell--compact">
        <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta">
            <a class="text-link" href="planes.php">Planes</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <span>Solicitar Pro</span>
        </nav>

        <header class="section-header page-planes-header">
            <p class="section-eyebrow">Plan Pro</p>
            <h1 class="section-title">Solicitar Pro</h1>
            <p class="section-lead">
                Subí el comprobante del pago acordado. Un administrador lo revisa y activa
                el plan Pro con la fecha de vencimiento correspondiente.
            </p>
        </header>

        <section class="card glass-panel">
            <?php if (isset($_GET['ok'])) : ?>
                <p class="auth-hint is-ok" role="status">
                    Solicitud enviada. Queda pendiente hasta que un administrador la revise.
                </p>
            <?php endif; ?>

            <?php if ($error !== '') : ?>
                <p class="auth-hint is-error" role="alert"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($esPro) : ?>
                <p class="auth-hint is-ok">
                    Ya tenés el plan Pro activo
                    <?php if ($usuario['plan_vencimiento']) : ?>
                        hasta el <?= date('d/m/Y', strtotime($usuario['plan_vencimiento'])) ?>.
                    <?php endif; ?>
                </p>
            <?php elseif ($pendiente > 0) : ?>
                <p class="auth-hint">
                    Tu solicitud está pendiente de revisión. Te avisamos cuando el administrador la resuelva.
                </p>
            <?php else : ?>
                <form class="auth-form" method="post" action="solicitar-pro.php" enctype="multipart/form-data">
                    <div class="auth-field">
                        <label class="auth-label" for="comprobante">
                            Comprobante de pago (JPG, PNG o PDF, hasta 5 MB)
                        </label>
                        <input
                            type="file"
                            id="comprobante"
                            name="comprobante"
                            class="auth-input"
                            accept=".jpg,.jpeg,.png,.pdf"
                            required
                        />
                    </div>

                    <button type="submit" class="auth-submit">
                        <span class="auth-submit__text">Enviar solicitud</span>
                        <span class="auth-submit__shine" aria-hidden="true"></span>
                    </button>
                </form>
            <?php endif; ?>
        </section>

        <?php if ($solicitudes) : ?>
            <section class="card glass-panel plans-compare">
                <h2 class="plans-block__title">Mis solicitudes</h2>
                <table class="plans-table">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Comprobante</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Resolución</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $solicitud) : ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?></td>
                                <td>
                                    <?php if (file_exists(__DIR__ . '/../' . $solicitud['comprobante'])) : ?>
                                        <a class="text-link" href="../<?= htmlspecialchars($solicitud['comprobante']) ?>" target="_blank">Ver archivo</a>
                                    <?php else : ?>
                                        Archivo no disponible
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($solicitud['estado'] === 'aprobada') : ?>
                                        <span class="course-badge course-badge--activo">Aprobada</span>
                                    <?php elseif ($solicitud['estado'] === 'rechazada') : ?>
                                        <span class="course-badge course-badge--finalizado">Rechazada</span>
                                    <?php else : ?>
                                        <span class="course-badge course-badge--proximo">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $solicitud['fecha_resolucion']
                                        ? date('d/m/Y', strtotime($solicitud['fecha_resolucion']))
                                        : '—' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
