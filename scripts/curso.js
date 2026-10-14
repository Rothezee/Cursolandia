const CURSOS_URL = "../data/cursos.json";
const FORO_URL = "../data/foro-hilos.json";

const forumState = {
    cursoId: null,
    hilos: [],
    query: "",
    status: "todos",
};

let currentCurso = null;

function $(id) {
    return document.getElementById(id);
}

function getCursoId() {
    const id = Number(new URLSearchParams(window.location.search).get("id"));
    return Number.isFinite(id) ? id : null;
}

function normalize(str) {
    return str
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{Diacritic}/gu, "");
}

function priceLabel(price) {
    return price === "gratis" ? "Gratis" : "Pago (acordar con el docente)";
}

function accessLabel(access) {
    return access === "publico" ? "Público" : "Privado";
}

function formatDate(iso) {
    if (!iso) return "—";
    const [y, m, d] = iso.split("-");
    return `${d}/${m}/${y}`;
}

function instructorInitials(name) {
    return name
        .split(/\s+/)
        .map((w) => w[0])
        .join("")
        .slice(0, 2)
        .toUpperCase();
}

function tabFromHash() {
    const hash = window.location.hash.replace("#", "");
    if (hash === "foro" || hash === "material" || hash === "info") return hash;
    return "info";
}

function setActiveTab(tab) {
    const tabs = ["info", "material", "foro"];
    tabs.forEach((t) => {
        const btn = $(`tab-${t}`);
        const panel = $(`panel-${t}`);
        const active = t === tab;
        btn?.classList.toggle("is-active", active);
        btn?.setAttribute("aria-selected", String(active));
        if (panel) panel.hidden = !active;
    });
    history.replaceState(null, "", `?id=${forumState.cursoId}${tab === "info" ? "" : `#${tab}`}`);
}

function initTabs() {
    document.querySelectorAll(".curso-tab").forEach((btn) => {
        btn.addEventListener("click", () => setActiveTab(btn.dataset.tab));
    });
    $("curso-jump-foro")?.addEventListener("click", () => setActiveTab("foro"));
    setActiveTab(tabFromHash());
}

function renderDetalles(curso) {
    const dl = $("curso-detalles");
    dl.replaceChildren();

    const items = [
        ["Vigencia", `${formatDate(curso.fechaInicio)} → ${formatDate(curso.fechaFin)}`],
        ["Estado del curso", curso.status === "activo" ? "Activo" : curso.status],
        ["Acceso", accessLabel(curso.access)],
        ["Modalidad de pago", priceLabel(curso.price)],
        [
            "Cupo",
            curso.cupo == null ? "Sin límite" : `${curso.inscriptos ?? 0} / ${curso.cupo} inscriptos`,
        ],
    ];

    items.forEach(([label, value]) => {
        const dt = document.createElement("dt");
        dt.textContent = label;
        const dd = document.createElement("dd");
        dd.textContent = value;
        dl.append(dt, dd);
    });
}

function renderMateriales(curso) {
    const list = $("curso-materiales");
    list.replaceChildren();
    const mats = curso.materiales ?? [];

    if (mats.length === 0) {
        const li = document.createElement("li");
        li.className = "empty-state empty-state--compact";
        li.textContent = "El docente aún no publicó archivos en este curso.";
        list.appendChild(li);
        return;
    }

    mats.forEach((file) => {
        const li = document.createElement("li");
        li.className = "curso-material-item";
        const icon = document.createElement("span");
        icon.className = "curso-material-item__icon";
        icon.setAttribute("aria-hidden", "true");
        const info = document.createElement("div");
        const name = document.createElement("span");
        name.className = "curso-material-item__name";
        name.textContent = file.nombre;
        const size = document.createElement("span");
        size.className = "curso-material-item__size";
        size.textContent = file.tamano;
        info.append(name, size);
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "forum-action-btn";
        btn.textContent = "Descargar";
        btn.disabled = true;
        btn.title = "Disponible al conectar archivos en el servidor";
        li.append(icon, info, btn);
        list.appendChild(li);
    });
}

function renderSidebar(curso, forumCount) {
    $("curso-sidebar-price").textContent = curso.price === "gratis" ? "Gratis" : "Curso pago";
    $("curso-sidebar-access").textContent = accessLabel(curso.access);

    const enrollLabel = $("curso-enroll-label");
    const note = $("curso-sidebar-note");
    const btn = $("curso-enroll-btn");

    if (curso.cupo != null && (curso.inscriptos ?? 0) >= curso.cupo) {
        enrollLabel.textContent = "Cupo completo";
        note.textContent = "No quedan lugares. Consultá otras fechas o cursos.";
        btn.disabled = true;
    } else if (curso.price === "pago") {
        enrollLabel.textContent = "Solicitar inscripción";
        note.textContent =
            "En cursos pagos la inscripción queda pendiente hasta que el docente confirme el pago (gestión externa).";
        btn.disabled = true;
    } else {
        enrollLabel.textContent = "Inscribirme";
        note.textContent = "Necesitás iniciar sesión para anotarte (próximamente).";
        btn.disabled = true;
    }

    const stats = $("curso-sidebar-stats");
    stats.replaceChildren();
    [
        ["Inscriptos", String(curso.inscriptos ?? "—")],
        ["Preguntas en el foro", String(forumCount)],
        ["Etiquetas", String(curso.tags?.length ?? 0)],
    ].forEach(([label, value]) => {
        const li = document.createElement("li");
        li.innerHTML = `<span>${label}</span><strong>${value}</strong>`;
        stats.appendChild(li);
    });

    $("curso-sidebar").hidden = false;
}

function renderCursoHeader(curso) {
    currentCurso = curso;
    $("curso-breadcrumb-title").textContent = curso.title;
    document.title = `${curso.title} — Cursolandia`;

    const badges = $("curso-hero-badges");
    badges.replaceChildren();
    if (curso.featured) {
        const pro = document.createElement("span");
        pro.className = "course-badge course-badge--pro";
        pro.textContent = "Destacado Pro";
        badges.appendChild(pro);
    }
    const price = document.createElement("span");
    price.className = `course-badge course-badge--${curso.price}`;
    price.textContent = curso.price === "gratis" ? "Gratis" : "Pago";
    badges.appendChild(price);
    const access = document.createElement("span");
    access.className = "forum-badge forum-badge--abierta";
    access.textContent = accessLabel(curso.access);
    badges.appendChild(access);

    $("curso-title").textContent = curso.title;
    $("curso-description").textContent = curso.description;
    $("curso-info-body").textContent = curso.longDescription || curso.description;

    const tags = $("curso-tags");
    tags.replaceChildren();
    (curso.tags || []).forEach((tag) => {
        const li = document.createElement("li");
        li.textContent = tag;
        tags.appendChild(li);
    });

    $("curso-docente-name").textContent = curso.instructor;
    $("curso-docente-avatar").textContent = instructorInitials(curso.instructor);

    renderDetalles(curso);
    renderMateriales(curso);
    $("curso-hero").hidden = false;
}

function statusLabel(status) {
    return status === "cerrada" ? "Cerrada" : "Abierta";
}

function filterHilos() {
    return forumState.hilos.filter((h) => {
        if (h.cursoId !== forumState.cursoId) return false;
        if (forumState.status !== "todos" && h.status !== forumState.status) return false;
        if (!forumState.query) return true;
        const q = normalize(forumState.query);
        return (
            normalize(h.title).includes(q) ||
            normalize(h.excerpt).includes(q) ||
            (h.tags || []).some((t) => normalize(t).includes(q))
        );
    });
}

function createThreadCard(hilo) {
    const li = document.createElement("li");
    const article = document.createElement("article");
    article.className = "forum-thread card";

    const badges = document.createElement("div");
    badges.className = "forum-thread__badges";
    const statusBadge = document.createElement("span");
    statusBadge.className = `forum-badge forum-badge--${hilo.status}`;
    statusBadge.textContent = statusLabel(hilo.status);
    badges.appendChild(statusBadge);
    if (hilo.status === "cerrada" && hilo.answersCount > 0) {
        const accepted = document.createElement("span");
        accepted.className = "forum-badge forum-badge--accepted";
        accepted.textContent = "Respuesta aceptada";
        badges.appendChild(accepted);
    }

    const title = document.createElement("h3");
    title.className = "forum-thread__title";
    const link = document.createElement("a");
    link.href = `foro-hilo.php?curso=${forumState.cursoId}&id=${hilo.id}`;
    link.textContent = hilo.title;
    title.appendChild(link);

    const excerpt = document.createElement("p");
    excerpt.className = "forum-thread__excerpt";
    excerpt.textContent = hilo.excerpt;

    const meta = document.createElement("div");
    meta.className = "forum-thread__meta";
    const by = document.createElement("span");
    const strong = document.createElement("strong");
    strong.textContent = hilo.author;
    by.append("Por ", strong, ` · ${hilo.role}`);
    const replies = document.createElement("span");
    replies.textContent = `${hilo.answersCount} respuesta${hilo.answersCount === 1 ? "" : "s"}`;
    const date = document.createElement("span");
    date.textContent = hilo.createdAt;
    meta.append(by, replies, date);

    const tags = document.createElement("ul");
    tags.className = "forum-thread__tags";
    (hilo.tags || []).forEach((tag) => {
        const t = document.createElement("li");
        t.textContent = tag;
        tags.appendChild(t);
    });

    article.append(badges, title, excerpt, meta, tags);
    li.appendChild(article);
    return li;
}

function renderForum() {
    const list = filterHilos();
    const listEl = $("forum-thread-list");
    listEl.replaceChildren();

    if (list.length === 0) {
        const empty = document.createElement("p");
        empty.className = "empty-state";
        empty.textContent = "No hay preguntas en este curso con esos filtros.";
        listEl.appendChild(empty);
    } else {
        list.forEach((h) => listEl.appendChild(createThreadCard(h)));
    }

    const total = forumState.hilos.filter((h) => h.cursoId === forumState.cursoId).length;
    $("forum-count").textContent =
        list.length === total
            ? `${list.length} pregunta${list.length === 1 ? "" : "s"} en este curso`
            : `${list.length} de ${total} preguntas`;

    if (currentCurso) {
        renderSidebar(currentCurso, total);
    }
}

function bindForumEvents() {
    $("forum-search").addEventListener("input", (e) => {
        forumState.query = e.target.value.trim();
        renderForum();
    });

    $("filter-forum-status").addEventListener("click", (e) => {
        const chip = e.target.closest(".filter-chip");
        if (!chip) return;
        forumState.status = chip.dataset.status;
        $("filter-forum-status").querySelectorAll(".filter-chip").forEach((c) => {
            c.classList.toggle("is-active", c.dataset.status === forumState.status);
        });
        renderForum();
    });

    $("forum-filters-reset").addEventListener("click", () => {
        $("forum-search").value = "";
        forumState.query = "";
        forumState.status = "todos";
        $("filter-forum-status").querySelectorAll(".filter-chip").forEach((c) => {
            c.classList.toggle("is-active", c.dataset.status === "todos");
        });
        renderForum();
    });
}

function showError(msg) {
    const err = $("curso-error");
    err.textContent = msg;
    err.hidden = false;
}

async function init() {
    const cursoId = getCursoId();
    if (!cursoId) {
        showError("Curso no especificado. Volvé al catálogo y elegí un curso.");
        return;
    }
    forumState.cursoId = cursoId;

    try {
        const [cRes, fRes] = await Promise.all([fetch(CURSOS_URL), fetch(FORO_URL)]);
        if (!cRes.ok || !fRes.ok) throw new Error("fetch");
        const cData = await cRes.json();
        const fData = await fRes.json();
        const curso = (cData.cursos ?? []).find((c) => c.id === cursoId);
        if (!curso) {
            showError("No encontramos ese curso.");
            return;
        }
        forumState.hilos = fData.hilos ?? [];
        renderCursoHeader(curso);
        const forumCount = forumState.hilos.filter((h) => h.cursoId === cursoId).length;
        renderSidebar(curso, forumCount);
        initTabs();
        bindForumEvents();
        renderForum();
    } catch (e) {
        console.error(e);
        showError("No se pudo cargar el curso. Abrí la página desde AppServ.");
    }
}

document.addEventListener("DOMContentLoaded", init);
