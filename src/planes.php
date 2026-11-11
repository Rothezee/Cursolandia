<?php
session_start();
$base = '..';
$navPage = 'planes';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"/>
    <link rel="icon" href="../img/logo.svg" type="image/x-icon">
    <title>Planes — Cursolandia</title>
</head>
<body class="page-planes">
    <?php require __DIR__ . '/../includes/nav.php'; ?>

    <main class="page-shell">
        <header class="section-header page-planes-header">
            <p class="section-eyebrow">Plan gratuito y Pro</p>
            <h1 class="section-title">Más alcance cuando lo necesitás</h1>
            <p class="section-lead">
                Empezá gratis con la comunidad completa. Si publicás o estudiás mucho, Pro amplía
                límites de cursos, inscripciones y archivos, y destaca tus cursos en el catálogo.
            </p>
        </header>

        <div class="plans-grid">
            <article class="plan-card card glass-panel" aria-labelledby="plan-gratuito-title">
                <header class="plan-card__head">
                    <h2 class="plan-card__name" id="plan-gratuito-title">Gratuito</h2>
                    <p class="plan-card__tagline">Ideal para probar y participar</p>
                    <p class="plan-card__price"><span class="plan-card__amount">$0</span> / para siempre</p>
                </header>
                <ul class="plan-card__list">
                    <li>Perfil completo con intereses y avatar</li>
                    <li>Foro Q&amp;A en cada curso</li>
                    <li>Cursos públicos o privados, gratis o de pago</li>
                </ul>
                <a href="registro.php" class="btn-outline btn-outline--cyan plan-card__cta">Crear cuenta gratis</a>
            </article>

            <article class="plan-card card glass-panel plan-card--featured card-accent-pro" aria-labelledby="plan-pro-title">
                <span class="plan-card__ribbon" aria-hidden="true">Recomendado</span>
                <header class="plan-card__head">
                    <h2 class="plan-card__name" id="plan-pro-title">Pro</h2>
                    <p class="plan-card__tagline">Para docentes y alumnos intensivos</p>
                    <p class="plan-card__price"><span class="plan-card__amount">A convenir</span> / activación con vencimiento</p>
                </header>
                <ul class="plan-card__list">
                    <li>Cursos activos e inscripciones sin tope</li>
                    <li>Más archivos y mayor tamaño por material</li>
                    <li>Cursos destacados en búsqueda y recomendaciones</li>
                </ul>
                <a href="registro.php" class="auth-submit plan-card__cta">
                    <span class="auth-submit__text">Solicitar Pro</span>
                    <span class="auth-submit__shine" aria-hidden="true"></span>
                </a>
                <p class="plan-card__hint auth-hint">
                    Registrate primero; la solicitud con comprobante se habilitará en tu perfil.
                </p>
            </article>
        </div>

        <div class="glass-panel plans-compare">
            <h2 class="plans-block__title">Comparativa detallada</h2>
            <table class="plans-table">
                <caption class="plans-table__caption">Límites y beneficios por plan</caption>
                <thead>
                    <tr>
                        <th scope="col">Característica</th>
                        <th scope="col">Gratuito</th>
                        <th scope="col">Pro</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="plans-table__feature">Cursos activos como creador</th>
                        <td>Hasta 3</td>
                        <td class="plans-table__pro">Sin tope</td>
                    </tr>
                    <tr>
                        <th class="plans-table__feature">Inscripciones simultáneas como alumno</th>
                        <td>Hasta 3</td>
                        <td class="plans-table__pro">Sin tope</td>
                    </tr>
                    <tr>
                        <th class="plans-table__feature">Archivos por curso</th>
                        <td>Hasta 5</td>
                        <td class="plans-table__pro">Hasta 20</td>
                    </tr>
                    <tr>
                        <th class="plans-table__feature">Tamaño máximo por archivo</th>
                        <td>5 MB</td>
                        <td class="plans-table__pro">25 MB</td>
                    </tr>
                    <tr>
                        <th class="plans-table__feature">Destacado en catálogo</th>
                        <td>—</td>
                        <td class="plans-table__pro">Sí</td>
                    </tr>
                    <tr>
                        <th class="plans-table__feature">Foro Q&amp;A y perfiles</th>
                        <td>Incluido</td>
                        <td class="plans-table__pro">Incluido</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <section class="plans-activation" aria-labelledby="plans-activation-title">
            <h2 id="plans-activation-title" class="plans-block__title">Activación manual de Pro</h2>
            <p class="section-lead plans-activation__lead">
                El pago se acuerda fuera del sistema (transferencia u otro medio). El administrador
                valida el comprobante y activa tu plan con fecha de vencimiento.
            </p>
            <ol class="plans-steps">
                <li class="plans-step card">
                    <span class="plans-step__num">1</span>
                    <h3 class="plans-step__title">Registrate</h3>
                    <p class="plans-step__text">Creá tu cuenta con el plan gratuito por defecto.</p>
                </li>
                <li class="plans-step card">
                    <span class="plans-step__num">2</span>
                    <h3 class="plans-step__title">Enviá comprobante</h3>
                    <p class="plans-step__text">Desde tu perfil subís el comprobante de pago y el período solicitado (próximamente en el portal).</p>
                </li>
                <li class="plans-step card">
                    <span class="plans-step__num">3</span>
                    <h3 class="plans-step__title">Confirmación admin</h3>
                    <p class="plans-step__text">Un administrador revisa el pago y activa Pro hasta la fecha de vencimiento acordada.</p>
                </li>
                <li class="plans-step card">
                    <span class="plans-step__num">4</span>
                    <h3 class="plans-step__title">Disfrutá los beneficios</h3>
                    <p class="plans-step__text">Tus cursos pueden marcarse como destacados y aplican los límites ampliados.</p>
                </li>
            </ol>
        </section>

        <section class="plans-faq">
            <h2 class="plans-block__title">Preguntas frecuentes</h2>
            <div class="plans-faq__list">
                <details class="plans-faq__item card">
                    <summary class="plans-faq__q">¿Puedo volver al plan gratuito?</summary>
                    <p class="plans-faq__a">Sí. Al vencer Pro volvés a los límites gratuitos; tus cursos y datos se mantienen.</p>
                </details>
                <details class="plans-faq__item card">
                    <summary class="plans-faq__q">¿Los cursos de pago pasan por Cursolandia?</summary>
                    <p class="plans-faq__a">No. Los aranceles de cursos pagos se acuerdan con el docente; la plataforma no procesa cobros de inscripción.</p>
                </details>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
