<?php
/** Uso: en index.php → $base = ''; en src/cursos.php → $base = '..'; */
$base = $base ?? '';
$assetPrefix = $base === '' ? '' : $base . '/';
$home = $base === '' ? 'index.php' : '../index.php';
$cursos = $base === '' ? 'src/cursos.php' : 'cursos.php';
$planes = $base === '' ? 'src/planes.php' : 'planes.php';
$login = $base === '' ? 'src/login.php' : 'login.php';
$perfil = $base === '' ? 'src/perfil.php' : 'perfil.php';
$navPage = $navPage ?? 'inicio'; // inicio | cursos | planes | login | registro | perfil
$loggedIn = !empty($_SESSION['usuario_id']);
?>
<nav class="site-nav" aria-label="Principal">
    <div class="nav-inner">
        <a class="logo" href="<?= htmlspecialchars($home) ?>" aria-label="Cursolandia inicio">
            <img src="<?= htmlspecialchars($assetPrefix) ?>img/logo-nav.svg" alt="Cursolandia.com" width="348" height="70">
        </a>
        <div class="hamburger" aria-label="Menú" role="button" tabindex="0">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
        <ul class="nav-links">
            <li>
                <a href="<?= htmlspecialchars($home) ?>#inicio"<?= $navPage === 'inicio' ? ' class="is-active" aria-current="page"' : '' ?>>Inicio</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($cursos) ?>"<?= $navPage === 'cursos' ? ' class="is-active" aria-current="page"' : '' ?>>Cursos</a>
            </li>
            <li>
                <a href="<?= htmlspecialchars($planes) ?>"<?= $navPage === 'planes' ? ' class="is-active" aria-current="page"' : '' ?>>Planes</a>
            </li>
            <li>
                <?php if ($loggedIn) : ?>
                    <a href="<?= htmlspecialchars($perfil) ?>" class="login-button<?= $navPage === 'perfil' ? ' is-active' : '' ?>"<?= $navPage === 'perfil' ? ' aria-current="page"' : '' ?>>Perfil</a>
                <?php else : ?>
                    <a href="<?= htmlspecialchars($login) ?>" class="login-button<?= $navPage === 'login' ? ' is-active' : '' ?>"<?= $navPage === 'login' ? ' aria-current="page"' : '' ?>>Ingresar</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>
