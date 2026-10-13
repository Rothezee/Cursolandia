<?php
$base = '..';
$navPage = 'login';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Ingresar — Cursolandia</title>
</head>
<body class="page-login">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="login-main">
        <div class="login-orb login-orb--1" aria-hidden="true"></div>
        <div class="login-orb login-orb--2" aria-hidden="true"></div>

        <section class="login-card" aria-labelledby="login-title">
            <header class="login-card__header">
                <p class="section-eyebrow login-card__eyebrow">Acceso</p>
                <h1 id="login-title" class="login-card__title">Bienvenido de nuevo</h1>
                <p class="login-card__subtitle">
                    Ingresá a tu cuenta para crear cursos, participar en el foro y gestionar tu perfil.
                </p>
            </header>

            <form class="auth-form" id="login-form" action="#" method="post" novalidate>
                <div class="auth-field auth-field--animate" style="--i: 0">
                    <label class="auth-label" for="login-email">Correo electrónico</label>
                    <input
                        type="email"
                        id="login-email"
                        name="email"
                        class="auth-input"
                        placeholder="tu@email.com"
                        autocomplete="email"
                        required
                    />
                </div>

                <div class="auth-field auth-field--animate" style="--i: 1">
                    <label class="auth-label" for="login-password">Contraseña</label>
                    <div class="auth-input-wrap">
                        <input
                            type="password"
                            id="login-password"
                            name="password"
                            class="auth-input"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        />
                        <button
                            type="button"
                            class="auth-toggle-pw"
                            id="toggle-password"
                            aria-label="Mostrar contraseña"
                            aria-pressed="false"
                        >
                            <span class="auth-toggle-pw__show">Mostrar</span>
                            <span class="auth-toggle-pw__hide" hidden>Ocultar</span>
                        </button>
                    </div>
                </div>

                <div class="auth-row auth-field--animate" style="--i: 2">
                    <label class="auth-remember">
                        <input type="checkbox" name="remember" class="filter-check"/>
                        <span>Recordarme en este equipo</span>
                    </label>
                    <a href="#" class="auth-link auth-link--muted" tabindex="-1" title="Próximamente">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="auth-submit auth-field--animate" style="--i: 3">
                    <span class="auth-submit__text">Ingresar</span>
                    <span class="auth-submit__shine" aria-hidden="true"></span>
                </button>
            </form>

            <p class="login-card__footer auth-field--animate" style="--i: 4">
                ¿No tenés cuenta?
                <a href="registro.php" class="auth-link">Registrate</a>
            </p>
        </section>

        <p class="login-back">
            <a class="text-link" href="../index.php">← Volver al inicio</a>
        </p>
    </main>

    <script src="../scripts/auth-common.js" defer></script>
    <script src="../scripts/login.js" defer></script>
    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
