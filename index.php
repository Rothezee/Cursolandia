<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css"/>
    <link rel="icon" href="img/logo.svg" type="image/x-icon">
    <title>Cursolandia — Comunidad, cursos y foro Q&amp;A</title>
</head>
<body>
    <nav class="site-nav" aria-label="Principal">
        <div class="nav-inner">
        <a class="logo" href="#inicio" aria-label="Cursolandia inicio">
            <img src="img/logo-nav.svg" alt="Cursolandia.com" width="348" height="70">
        </a>
        <div class="hamburger" aria-label="Menú" role="button" tabindex="0">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
        <ul class="nav-links">
            <li><a href="#inicio">Inicio</a></li>
            <li><a href="#cursos">Cursos</a></li>
            <li><a href="#foro">Foro</a></li>
            <li><a href="#planes">Planes</a></li>
            <li><button class="login-button" type="button">Ingresar</button></li>
        </ul>
        </div>
    </nav>

    <main id="inicio">
        <div id="sections-root" class="sections-root" aria-live="polite"></div>
    </main>

    <footer class="site-footer">
        <p class="footer-brand">Cursolandia.com</p>
        <p class="footer-tagline">Comunidad · Cursos · Foro Q&amp;A</p>
        <p class="footer-copy">© 2026 Cursolandia.</p>
    </footer>
    <script src="scripts/home-sections.js" defer></script>
    <script src="scripts/main.js" defer></script>
</body>
</html>
