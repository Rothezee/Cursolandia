<?php
session_start();
require_once __DIR__ . '/../includes/conn.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$base = '..';
$navPage = 'perfil';
$usuarioId = (int) $_SESSION['usuario_id'];

$rol = $conn->query("SELECT rol FROM usuarios WHERE id = $usuarioId")->fetchColumn();

if ($rol !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $solicitudId = (int) ($_POST['solicitud_id'] ?? 0);

    if ($accion === 'aprobar') {
        $meses = (int) ($_POST['meses'] ?? 1);

        if (!in_array($meses, [1, 3, 6, 12])) {
            $meses = 1;
        }

        $solicitud = $conn->query("SELECT usuario_id FROM solicitudes_pro
            WHERE id = $solicitudId AND estado = 'pendiente'")->fetch();

        if ($solicitud) {
            $alumnoId = (int) $solicitud['usuario_id'];
            $vencimiento = date('Y-m-d', strtotime("+$meses months"));
            $vencimientoSql = $conn->quote($vencimiento);

            $conn->query("UPDATE usuarios SET plan = 'pro', plan_vencimiento = $vencimientoSql
                WHERE id = $alumnoId");
            $conn->query("UPDATE solicitudes_pro SET estado = 'aprobada', fecha_resolucion = CURDATE()
                WHERE id = $solicitudId");
        }

        header('Location: admin-pro.php?ok=aprobada');
        exit;
    }

    if ($accion === 'rechazar') {
        $conn->query("UPDATE solicitudes_pro SET estado = 'rechazada', fecha_resolucion = CURDATE()
            WHERE id = $solicitudId AND estado = 'pendiente'");
        header('Location: admin-pro.php?ok=rechazada');
        exit;
    }

    if ($accion === 'quitar') {
        $alumnoId = (int) ($_POST['usuario_id'] ?? 0);
        $conn->query("UPDATE usuarios SET plan = 'gratuito', plan_vencimiento = NULL
            WHERE id = $alumnoId");
        header('Location: admin-pro.php?ok=quitado');
        exit;
    }
}

$pendientes = $conn->query("SELECT s.*, u.usuario, u.nombre, u.apellido
    FROM solicitudes_pro s
    JOIN usuarios u ON u.id = s.usuario_id
    WHERE s.estado = 'pendiente'
    ORDER BY s.fecha_solicitud")->fetchAll();

$historial = $conn->query("SELECT s.*, u.usuario
    FROM solicitudes_pro s
    JOIN usuarios u ON u.id = s.usuario_id
    WHERE s.estado <> 'pendiente'
    ORDER BY s.id DESC
    LIMIT 10")->fetchAll();

$pros = $conn->query("SELECT id, usuario, nombre, apellido, plan_vencimiento
    FROM usuarios
    WHERE plan = 'pro'
    ORDER BY plan_vencimiento")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Admin Pro — Cursolandia</title>
</head>
<body class="page-planes">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell">
        <nav class="page-breadcrumb curso-breadcrumb" aria-label="Ruta">
            <a class="text-link" href="../index.php">Inicio</a>
            <span class="curso-breadcrumb__sep" aria-hidden="true">/</span>
            <span>Admin Pro</span>
        </nav>

        <header class="section-header page-planes-header">
            <p class="section-eyebrow">Administración</p>
            <h1 class="section-title">Solicitudes de plan Pro</h1>
            <p class="section-lead">
                Revisá el comprobante y activá el plan Pro con el período acordado.
                Al vencer, el usuario vuelve automáticamente al plan gratuito.
            </p>
        </header>

        <?php if (isset($_GET['ok'])) : ?>
            <p class="auth-hint is-ok" role="status">
                <?php if ($_GET['ok'] === 'aprobada') : ?>
                    Solicitud aprobada y plan Pro activado.
                <?php elseif ($_GET['ok'] === 'rechazada') : ?>
                    Solicitud rechazada.
                <?php else : ?>
                    Plan Pro quitado.
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <section class="card glass-panel plans-compare">
            <h2 class="plans-block__title">Pendientes (<?= count($pendientes) ?>)</h2>

            <?php if (!$pendientes) : ?>
                <p class="auth-hint">No hay solicitudes pendientes.</p>
            <?php else : ?>
                <table class="plans-table">
                    <thead>
                        <tr>
                            <th scope="col">Usuario</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Comprobante</th>
                            <th scope="col">Período</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendientes as $solicitud) : ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($solicitud['nombre'] . ' ' . $solicitud['apellido']) ?>
                                    <small>(<?= htmlspecialchars($solicitud['usuario']) ?>)</small>
                                </td>
                                <td><?= date('d/m/Y', strtotime($solicitud['fecha_solicitud'])) ?></td>
                                <td>
                                    <?php if (file_exists(__DIR__ . '/../' . $solicitud['comprobante'])) : ?>
                                        <a class="text-link" href="../<?= htmlspecialchars($solicitud['comprobante']) ?>" target="_blank">Ver</a>
                                    <?php else : ?>
                                        Sin archivo
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form class="admin-actions" method="post" action="admin-pro.php">
                                        <input type="hidden" name="accion" value="aprobar"/>
                                        <input type="hidden" name="solicitud_id" value="<?= (int) $solicitud['id'] ?>"/>
                                        <select name="meses" class="auth-input filter-select">
                                            <option value="1">1 mes</option>
                                            <option value="3">3 meses</option>
                                            <option value="6">6 meses</option>
                                            <option value="12">12 meses</option>
                                        </select>
                                        <button type="submit" class="forum-action-btn">Aprobar</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" action="admin-pro.php">
                                        <input type="hidden" name="accion" value="rechazar"/>
                                        <input type="hidden" name="solicitud_id" value="<?= (int) $solicitud['id'] ?>"/>
                                        <button type="submit" class="forum-action-btn forum-action-btn--danger">Rechazar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="card glass-panel plans-compare">
            <h2 class="plans-block__title">Usuarios con Pro (<?= count($pros) ?>)</h2>

            <?php if (!$pros) : ?>
                <p class="auth-hint">Todavía no hay usuarios con plan Pro.</p>
            <?php else : ?>
                <table class="plans-table">
                    <thead>
                        <tr>
                            <th scope="col">Usuario</th>
                            <th scope="col">Vence</th>
                            <th scope="col">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pros as $pro) : ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($pro['nombre'] . ' ' . $pro['apellido']) ?>
                                    <small>(<?= htmlspecialchars($pro['usuario']) ?>)</small>
                                </td>
                                <td>
                                    <?= $pro['plan_vencimiento']
                                        ? date('d/m/Y', strtotime($pro['plan_vencimiento']))
                                        : 'Sin fecha' ?>
                                </td>
                                <td>
                                    <form method="post" action="admin-pro.php">
                                        <input type="hidden" name="accion" value="quitar"/>
                                        <input type="hidden" name="usuario_id" value="<?= (int) $pro['id'] ?>"/>
                                        <button type="submit" class="forum-action-btn forum-action-btn--danger">Quitar Pro</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="card glass-panel plans-compare">
            <h2 class="plans-block__title">Últimas resoluciones</h2>

            <?php if (!$historial) : ?>
                <p class="auth-hint">No hay solicitudes resueltas.</p>
            <?php else : ?>
                <table class="plans-table">
                    <thead>
                        <tr>
                            <th scope="col">Usuario</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Resuelta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $solicitud) : ?>
                            <tr>
                                <td><?= htmlspecialchars($solicitud['usuario']) ?></td>
                                <td><?= $solicitud['estado'] === 'aprobada' ? 'Aprobada' : 'Rechazada' ?></td>
                                <td><?= date('d/m/Y', strtotime($solicitud['fecha_resolucion'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
