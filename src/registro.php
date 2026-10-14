<?php
$base = '..';
$navPage = 'registro';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Crear cuenta — Cursolandia</title>
</head>
<body class="page-login page-register">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="login-main login-main--register">
        <div class="login-orb login-orb--1" aria-hidden="true"></div>
        <div class="login-orb login-orb--2" aria-hidden="true"></div>

        <section class="login-card login-card--register glass-panel glass-panel--hero" aria-labelledby="register-title">
            <header class="login-card__header">
                <p class="section-eyebrow login-card__eyebrow">Comunidad Cursolandia</p>
                <h1 id="register-title" class="login-card__title">Creá tu cuenta</h1>
                <p class="login-card__subtitle">
                    Completá tu perfil con datos personales, intereses y antecedentes para personalizar
                    recomendaciones de cursos y participar en el foro Q&amp;A.
                </p>
                <p class="register-plan-badge auth-field--animate" style="--i: 0">
                    Plan inicial: <strong>Gratuito</strong> — podés solicitar Pro más adelante desde tu perfil.
                </p>
            </header>

            <form class="auth-form" id="registro-form" action="#" method="post" enctype="multipart/form-data" novalidate>
                <fieldset class="auth-section auth-field--animate" style="--i: 1">
                    <legend class="auth-section__title">Datos personales</legend>
                    <div class="auth-grid">
                        <div class="auth-field">
                            <label class="auth-label" for="reg-nombre">Nombre</label>
                            <input type="text" id="reg-nombre" name="nombre" class="auth-input" autocomplete="given-name" required />
                        </div>
                        <div class="auth-field">
                            <label class="auth-label" for="reg-apellido">Apellido</label>
                            <input type="text" id="reg-apellido" name="apellido" class="auth-input" autocomplete="family-name" required />
                        </div>
                    </div>
                    <div class="auth-field">
                        <label class="auth-label" for="reg-email">Correo electrónico</label>
                        <input
                            type="email"
                            id="reg-email"
                            name="email"
                            class="auth-input"
                            placeholder="tu@email.com"
                            autocomplete="email"
                            required
                        />
                    </div>
                    <div class="auth-field">
                        <label class="auth-label" for="reg-telefono">Teléfono <span class="auth-optional">(opcional)</span></label>
                        <input type="tel" id="reg-telefono" name="telefono" class="auth-input" autocomplete="tel" />
                    </div>
                </fieldset>

                <fieldset class="auth-section auth-field--animate" style="--i: 2">
                    <legend class="auth-section__title">Tu perfil en la plataforma</legend>
                    <div class="auth-field">
                        <label class="auth-label" for="reg-usuario">Nombre de usuario público</label>
                        <input
                            type="text"
                            id="reg-usuario"
                            name="usuario"
                            class="auth-input"
                            placeholder="cómo te verán en cursos y en el foro"
                            autocomplete="username"
                            required
                        />
                        <p class="auth-hint">Se muestra cuando creás preguntas, respondés o publicás un curso.</p>
                    </div>
                    <div class="auth-field">
                        <label class="auth-label" for="reg-intereses">Intereses</label>
                        <input
                            type="text"
                            id="reg-intereses"
                            name="intereses"
                            class="auth-input"
                            placeholder="php, diseño web, bases de datos…"
                        />
                        <p class="auth-hint">Separá con comas. Usamos esto para recomendarte cursos según sus etiquetas.</p>
                    </div>
                    <div class="auth-field">
                        <label class="auth-label" for="reg-antecedentes">Antecedentes</label>
                        <textarea
                            id="reg-antecedentes"
                            name="antecedentes"
                            class="auth-input auth-textarea"
                            rows="3"
                            placeholder="Formación, experiencia o lo que quieras compartir en tu perfil."
                        ></textarea>
                    </div>
                    <div class="auth-field">
                        <span class="auth-label">Imagen principal del perfil</span>
                        <div class="auth-avatar-row">
                            <div id="avatar-preview" class="avatar-preview" aria-hidden="true">
                                <span class="avatar-preview__placeholder">Tu foto</span>
                            </div>
                            <label class="auth-file">
                                <input type="file" id="reg-avatar" name="avatar" accept="image/*" class="auth-file__input" />
                                <span class="auth-file__label">Elegir imagen</span>
                                <span class="auth-hint">Aparecerá en tus acciones dentro del portal.</span>
                            </label>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="auth-section auth-field--animate" style="--i: 3">
                    <legend class="auth-section__title">Contraseña de acceso</legend>
                    <div class="auth-field">
                        <label class="auth-label" for="reg-password">Contraseña</label>
                        <div class="auth-input-wrap">
                            <input
                                type="password"
                                id="reg-password"
                                name="password"
                                class="auth-input"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            />
                            <button type="button" class="auth-toggle-pw" id="toggle-password" aria-label="Mostrar contraseña" aria-pressed="false">
                                <span class="auth-toggle-pw__show">Mostrar</span>
                                <span class="auth-toggle-pw__hide" hidden>Ocultar</span>
                            </button>
                        </div>
                        <p class="auth-hint">Mínimo 8 caracteres. Podrás cambiarla luego desde tu perfil.</p>
                    </div>
                    <div class="auth-field">
                        <label class="auth-label" for="reg-password-confirm">Confirmar contraseña</label>
                        <div class="auth-input-wrap">
                            <input
                                type="password"
                                id="reg-password-confirm"
                                name="password_confirm"
                                class="auth-input"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            />
                            <button type="button" class="auth-toggle-pw" id="toggle-password-confirm" aria-label="Mostrar contraseña" aria-pressed="false">
                                <span class="auth-toggle-pw__show">Mostrar</span>
                                <span class="auth-toggle-pw__hide" hidden>Ocultar</span>
                            </button>
                        </div>
                        <p class="auth-hint" id="password-match-hint" role="status"></p>
                    </div>
                </fieldset>

                <label class="auth-remember auth-field--animate" style="--i: 4">
                    <input type="checkbox" name="terminos" class="filter-check" required />
                    <span>Acepto participar en la comunidad y el uso de mis datos según las normas del proyecto.</span>
                </label>

                <button type="submit" class="auth-submit auth-field--animate" style="--i: 5">
                    <span class="auth-submit__text">Crear cuenta</span>
                    <span class="auth-submit__shine" aria-hidden="true"></span>
                </button>
            </form>

            <p class="login-card__footer auth-field--animate" style="--i: 6">
                ¿Ya tenés cuenta?
                <a href="login.php" class="auth-link">Ingresá</a>
            </p>
        </section>

        <p class="login-back">
            <a class="text-link" href="../index.php">← Volver al inicio</a>
        </p>
    </main>

    <script src="../scripts/auth-common.js" defer></script>
    <script src="../scripts/registro.js" defer></script>
    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
