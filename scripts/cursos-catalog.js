const DATA_URL = "../data/cursos.json";

const state = {
    cursos: [],
    query: "",
    price: "todos",
    access: "todos",
    tag: "todos",
    featuredOnly: false,
};

const els = {};

function $(id) {
    return document.getElementById(id);
}

function normalize(str) {
    return str
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{Diacritic}/gu, "");
}

function matchesQuery(curso, query) {
    if (!query) return true;
    const q = normalize(query);
    const inTitle = normalize(curso.title).includes(q);
    const inTags = curso.tags.some((t) => normalize(t).includes(q));
    const inDesc = normalize(curso.description).includes(q);
    return inTitle || inTags || inDesc;
}

function filterCursos() {
    return state.cursos.filter((curso) => {
        if (!matchesQuery(curso, state.query)) return false;
        if (state.price !== "todos" && curso.price !== state.price) return false;
        if (state.access !== "todos" && curso.access !== state.access) return false;
        if (state.tag !== "todos" && !curso.tags.includes(state.tag)) return false;
        if (state.featuredOnly && !curso.featured) return false;
        return true;
    });
}

function priceLabel(price) {
    return price === "gratis" ? "Gratis" : "Pago";
}

function accessLabel(access) {
    return access === "publico" ? "Público" : "Privado";
}

function createCourseCard(curso) {
    const article = document.createElement("article");
    article.className = "card course-card";
    if (curso.featured) article.classList.add("card-accent-pro");

    const badges = document.createElement("div");
    badges.className = "course-card__badges";
    if (curso.featured) {
        const pro = document.createElement("span");
        pro.className = "course-badge course-badge--pro";
        pro.textContent = "Destacado Pro";
        badges.appendChild(pro);
    }
    const priceBadge = document.createElement("span");
    priceBadge.className = `course-badge course-badge--${curso.price}`;
    priceBadge.textContent = priceLabel(curso.price);
    badges.appendChild(priceBadge);

    const title = document.createElement("h2");
    title.className = "card-title course-card__title";
    title.textContent = curso.title;

    const desc = document.createElement("p");
    desc.className = "card-text";
    desc.textContent = curso.description;

    const meta = document.createElement("p");
    meta.className = "course-card__meta";
    meta.textContent = `${accessLabel(curso.access)} · ${curso.instructor}`;

    const tags = document.createElement("ul");
    tags.className = "course-card__tags";
    curso.tags.forEach((tag) => {
        const li = document.createElement("li");
        li.textContent = tag;
        tags.appendChild(li);
    });

    const link = document.createElement("a");
    link.href = `curso.php?id=${curso.id}`;
    link.className = "btn-outline btn-outline--cyan course-card__cta";
    link.textContent = "Ver curso";

    article.append(badges, title, desc, meta, tags, link);
    return article;
}

function renderTagFilters(allTags) {
    const container = els.tagFilters;
    container.replaceChildren();

    const allBtn = document.createElement("button");
    allBtn.type = "button";
    allBtn.className = "filter-chip is-active";
    allBtn.dataset.tag = "todos";
    allBtn.textContent = "Todas las etiquetas";
    container.appendChild(allBtn);

    allTags.forEach((tag) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "filter-chip";
        btn.dataset.tag = tag;
        btn.textContent = tag;
        container.appendChild(btn);
    });
}

function setActiveTagChip(tag) {
    els.tagFilters.querySelectorAll(".filter-chip").forEach((chip) => {
        chip.classList.toggle("is-active", chip.dataset.tag === tag);
    });
}

function render() {
    const list = filterCursos();
    els.grid.replaceChildren();

    if (list.length === 0) {
        const empty = document.createElement("p");
        empty.className = "empty-state empty-state--grid";
        empty.textContent =
            "No hay cursos con esos criterios. Probá otro nombre o quitá algún filtro.";
        els.grid.appendChild(empty);
    } else {
        list.forEach((curso) => els.grid.appendChild(createCourseCard(curso)));
    }

    const total = state.cursos.length;
    els.count.textContent =
        list.length === total
            ? `${list.length} curso${list.length === 1 ? "" : "s"}`
            : `${list.length} de ${total} cursos`;
}

function readFiltersFromUI() {
    state.query = els.search.value.trim();
    state.price = els.price.value;
    state.access = els.access.value;
    state.featuredOnly = els.featured.checked;
}

function bindEvents() {
    els.search.addEventListener("input", () => {
        readFiltersFromUI();
        render();
    });

    [els.price, els.access].forEach((el) => {
        el.addEventListener("change", () => {
            readFiltersFromUI();
            render();
        });
    });

    els.featured.addEventListener("change", () => {
        readFiltersFromUI();
        render();
    });

    els.tagFilters.addEventListener("click", (e) => {
        const chip = e.target.closest(".filter-chip");
        if (!chip) return;
        state.tag = chip.dataset.tag;
        setActiveTagChip(state.tag);
        readFiltersFromUI();
        render();
    });

    els.reset.addEventListener("click", () => {
        els.search.value = "";
        els.price.value = "todos";
        els.access.value = "todos";
        els.featured.checked = false;
        state.tag = "todos";
        setActiveTagChip("todos");
        readFiltersFromUI();
        render();
    });
}

function collectTags(cursos) {
    const set = new Set();
    cursos.forEach((c) => c.tags.forEach((t) => set.add(t)));
    return [...set].sort((a, b) => a.localeCompare(b, "es"));
}

async function init() {
    els.search = $("course-search");
    els.price = $("filter-price");
    els.access = $("filter-access");
    els.featured = $("filter-featured");
    els.tagFilters = $("filter-tags");
    els.grid = $("cursos-grid");
    els.count = $("courses-count");
    els.reset = $("filters-reset");

    if (!els.grid) return;

    try {
        const res = await fetch(DATA_URL);
        if (!res.ok) throw new Error(res.status);
        const data = await res.json();
        state.cursos = data.cursos ?? [];
        renderTagFilters(collectTags(state.cursos));
        bindEvents();
        render();
    } catch (err) {
        console.error(err);
        els.grid.innerHTML =
            '<p class="empty-state empty-state--grid">No se pudo cargar el catálogo. Abrí la página desde AppServ (no como archivo local).</p>';
    }
}

document.addEventListener("DOMContentLoaded", init);
