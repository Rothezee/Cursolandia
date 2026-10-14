const DATA_URL = "../data/foro-hilos.json";

function $(id) {
    return document.getElementById(id);
}

function getParams() {
    const params = new URLSearchParams(window.location.search);
    return {
        cursoId: Number(params.get("curso")),
        hiloId: Number(params.get("id")),
    };
}

function createAnswerEl(answer, isClosed) {
    const article = document.createElement("article");
    article.className = "forum-answer card";
    if (answer.accepted) article.classList.add("forum-answer--accepted");
    if (answer.isTeacher) article.classList.add("forum-answer--teacher");

    const head = document.createElement("header");
    head.className = "forum-answer__head";

    const who = document.createElement("div");
    who.className = "forum-answer__who";
    const name = document.createElement("strong");
    name.textContent = answer.author;
    const role = document.createElement("span");
    role.className = "forum-answer__role";
    role.textContent = ` ${answer.role}`;
    who.append(name, role);

    const badges = document.createElement("div");
    badges.className = "forum-answer__badges";
    if (answer.accepted) {
        const b = document.createElement("span");
        b.className = "forum-badge forum-badge--accepted";
        b.textContent = "Aceptada";
        badges.appendChild(b);
    }
    if (answer.isTeacher) {
        const b = document.createElement("span");
        b.className = "forum-badge forum-badge--teacher";
        b.textContent = "Docente";
        badges.appendChild(b);
    }

    head.append(who, badges);

    const body = document.createElement("p");
    body.className = "forum-answer__body";
    body.textContent = answer.body;

    const foot = document.createElement("footer");
    foot.className = "forum-answer__foot";
    foot.textContent = answer.createdAt;

    article.append(head, body, foot);

    if (!isClosed && !answer.accepted) {
        const actions = document.createElement("div");
        actions.className = "forum-answer__actions";
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "forum-action-btn";
        btn.textContent = "Marcar como aceptada";
        btn.disabled = true;
        btn.title = "Disponible cuando conectes permisos de docente o autor";
        actions.appendChild(btn);
        article.appendChild(actions);
    }

    return article;
}

function renderThread(hilo, cursoId) {
    document.title = `${hilo.title} — Foro`;

    const cursoUrl = `curso.php?id=${cursoId}#foro`;
    const breadcrumbCurso = $("breadcrumb-curso");
    const breadcrumbForo = $("breadcrumb-foro");
    if (breadcrumbCurso) {
        breadcrumbCurso.href = `curso.php?id=${cursoId}`;
        breadcrumbCurso.textContent = hilo.curso;
    }
    if (breadcrumbForo) {
        breadcrumbForo.href = cursoUrl;
        breadcrumbForo.textContent = "Foro";
    }

    $("hilo-title").textContent = hilo.title;
    $("hilo-excerpt").textContent = hilo.excerpt;
    $("hilo-author").textContent = hilo.author;
    $("hilo-date").textContent = hilo.createdAt;

    const statusEl = $("hilo-status");
    statusEl.textContent = hilo.status === "cerrada" ? "Cerrada" : "Abierta";
    statusEl.className = `forum-badge forum-badge--${hilo.status}`;

    const closed = hilo.status === "cerrada";
    $("reply-form")?.classList.toggle("is-disabled", closed);

    const answersRoot = $("forum-answers");
    answersRoot.replaceChildren();

    if (!hilo.answers?.length) {
        const empty = document.createElement("p");
        empty.className = "empty-state";
        empty.textContent = "Todavía no hay respuestas. Sé el primero en ayudar.";
        answersRoot.appendChild(empty);
    } else {
        hilo.answers.forEach((a) => answersRoot.appendChild(createAnswerEl(a, closed)));
    }

    $("answers-count").textContent = String(hilo.answers?.length ?? 0);
}

async function init() {
    const { cursoId, hiloId } = getParams();
    const root = $("forum-hilo-root");

    if (!cursoId || !hiloId) {
        root.innerHTML = '<p class="empty-state">Enlace inválido. Abrí el hilo desde el foro del curso.</p>';
        return;
    }

    try {
        const res = await fetch(DATA_URL);
        if (!res.ok) throw new Error(res.status);
        const data = await res.json();
        const hilo = (data.hilos ?? []).find((h) => h.id === hiloId && h.cursoId === cursoId);
        if (!hilo) {
            root.innerHTML = '<p class="empty-state">No existe ese hilo en este curso.</p>';
            return;
        }
        renderThread(hilo, cursoId);
    } catch (err) {
        console.error(err);
        root.innerHTML = '<p class="empty-state">Error al cargar el hilo.</p>';
    }

    $("reply-form")?.addEventListener("submit", (e) => {
        e.preventDefault();
        const btn = e.target.querySelector(".auth-submit");
        btn?.classList.add("is-loading");
        setTimeout(() => btn?.classList.remove("is-loading"), 800);
    });
}

document.addEventListener("DOMContentLoaded", init);
