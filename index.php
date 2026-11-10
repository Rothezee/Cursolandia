<?php
session_start();
$base = '';
$navPage = 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css"/>
    <link rel="icon" href="img/logo.svg" type="image/x-icon">
    <title>Cursolandia — Comunidad, cursos y foro Q&amp;A</title>
</head>
<body class="page-home">
    <?php require __DIR__ . '/includes/nav.php'; ?>

    <main id="inicio">
        <section class="page-section section-hero" aria-labelledby="hero-title">
            <header class="section-header">
                <p class="section-eyebrow">Web 2.0 educativa</p>
                <h1 id="hero-title" class="section-title">Aprendé, enseñá y conectá en un solo lugar</h1>
                <p class="section-lead">
                    Cursolandia nuclea cursos creados por la comunidad: públicos o privados, gratuitos o de pago,
                    con perfiles, inscripciones y un foro de preguntas y respuestas con respuestas aceptadas.
                </p>
            </header>

            <div class="card-grid">
                <article class="card">
                    <span class="card-icon" aria-hidden="true">◎</span>
                    <h2 class="card-title">Cursos de la comunidad</h2>
                    <p class="card-text">
                        Cualquier usuario puede proponer un curso con título, descripción, etiquetas,
                        fechas de vigencia y cupo opcional.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">◇</span>
                    <h2 class="card-title">Perfiles vivos</h2>
                    <p class="card-text">
                        Datos personales, intereses, antecedentes e imagen que te representa en cada acción
                        dentro del portal.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">◈</span>
                    <h2 class="card-title">Inscripciones inteligentes</h2>
                    <p class="card-text">
                        Anotate mientras el curso está activo; en cursos pagos el creador confirma la
                        inscripción pendiente.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">✦</span>
                    <h2 class="card-title">Material compartido</h2>
                    <p class="card-text">
                        Los docentes suben archivos adjuntos; el plan gratuito incluye límites de cantidad
                        y tamaño pensados para empezar.
                    </p>
                </article>
            </div>
        </section>

        <section class="page-section section-courses" id="cursos" aria-labelledby="courses-title">
            <header class="section-header">
                <p class="section-eyebrow">Crear y tomar cursos</p>
                <h2 id="courses-title" class="section-title">Tu aula, con reglas claras</h2>
                <p class="section-lead">
                    Los creadores gestionan el contenido y el acceso; los alumnos participan dentro del
                    período de inicio y fin del curso.
                </p>
            </header>

            <div class="card-grid">
                <article class="card">
                    <span class="card-icon" aria-hidden="true">1</span>
                    <h3 class="card-title">Hasta 3 cursos activos</h3>
                    <p class="card-text">
                        En plan gratuito podés tener hasta tres cursos activos como creador y tres
                        inscripciones simultáneas como alumno.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">2</span>
                    <h3 class="card-title">Público o privado</h3>
                    <p class="card-text">
                        Definí visibilidad y costo: cursos abiertos a todos o con acceso restringido,
                        sin cobro o con arancel acordado fuera del sistema.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">3</span>
                    <h3 class="card-title">Vigencia programada</h3>
                    <p class="card-text">
                        Fecha de inicio y fin marcan cuándo el curso está activo y cuándo se pueden sumar
                        nuevos participantes.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">4</span>
                    <h3 class="card-title">Búsqueda y recomendaciones</h3>
                    <p class="card-text">
                        Encontrá cursos por título o etiquetas y recibí sugerencias según tus intereses
                        declarados en el perfil.
                    </p>
                </article>
            </div>
        </section>

        <section class="page-section section-forum" id="foro" aria-labelledby="forum-title">
            <header class="section-header">
                <p class="section-eyebrow">Foro Q&amp;A</p>
                <h2 id="forum-title" class="section-title">Preguntas con respuesta aceptada</h2>
                <p class="section-lead">
                    Dentro de cada curso, alumnos y docentes conversan en un foro ordenado: destacados,
                    cierre de temas y notificaciones que mantienen a todos al día.
                </p>
            </header>

            <div class="card-grid">
                <article class="card card-accent-green">
                    <span class="card-icon" aria-hidden="true">?</span>
                    <h3 class="card-title">Hilos de consulta</h3>
                    <p class="card-text">
                        Los alumnos abren preguntas y responden en turnos: no podés contestar dos veces
                        seguidas sin editar o esperar a otro participante.
                    </p>
                </article>
                <article class="card card-accent-green">
                    <span class="card-icon" aria-hidden="true">✓</span>
                    <h3 class="card-title">Respuesta aceptada</h3>
                    <p class="card-text">
                        El docente o quien preguntó marca la mejor respuesta, la destaca y cierra el tema,
                        como en los foros profesionales.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">⚡</span>
                    <h3 class="card-title">Rol docente</h3>
                    <p class="card-text">
                        El creador del curso responde, destaca o elimina preguntas cuando hace falta
                        moderar la conversación.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">◉</span>
                    <h3 class="card-title">Notificaciones</h3>
                    <p class="card-text">
                        Avisos al crear preguntas, al responder, al cerrar un hilo o si se elimina una
                        consulta, según quién participó.
                    </p>
                </article>
            </div>
        </section>

        <section class="page-section section-plans" id="planes" aria-labelledby="plans-title">
            <header class="section-header">
                <p class="section-eyebrow">Plan gratuito y Pro</p>
                <h2 id="plans-title" class="section-title">Más alcance cuando lo necesitás</h2>
                <p class="section-lead">
                    El plan Pro amplía límites de cursos, inscripciones y archivos, y hace que tus cursos
                    aparezcan destacados en búsquedas y recomendaciones.
                </p>
            </header>

            <div class="card-grid">
                <article class="card">
                    <span class="card-icon" aria-hidden="true">○</span>
                    <h3 class="card-title">Plan gratuito</h3>
                    <p class="card-text">
                        Ideal para probar la plataforma: perfiles completos, foro, cursos e inscripciones
                        con topes razonables para la comunidad.
                    </p>
                </article>
                <article class="card card-accent-pro">
                    <span class="card-icon" aria-hidden="true">★</span>
                    <h3 class="card-title">Plan Pro</h3>
                    <p class="card-text">
                        Sin tope de cursos activos ni de inscripciones simultáneas, más espacio para
                        materiales y presencia destacada en el catálogo.
                    </p>
                </article>
                <article class="card card-accent-pro">
                    <span class="card-icon" aria-hidden="true">↑</span>
                    <h3 class="card-title">Activación manual</h3>
                    <p class="card-text">
                        Desde el perfil enviás comprobante al administrador; él activa Pro con fecha de
                        vencimiento cuando confirma el pago.
                    </p>
                </article>
                <article class="card">
                    <span class="card-icon" aria-hidden="true">⟡</span>
                    <h3 class="card-title">Cursos en primer plano</h3>
                    <p class="card-text">
                        Los cursos de usuarios Pro se muestran destacados al buscar y en las
                        recomendaciones basadas en etiquetas e intereses.
                    </p>
                </article>
            </div>

            <p class="plans-home-cta">
                <a class="btn-outline btn-outline--green plans-home-cta__link" href="src/planes.php">
                    Ver comparativa completa de planes
                </a>
            </p>
        </section>
    </main>

    <?php require __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
