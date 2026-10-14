const ACCENT_CLASS = {
    green: "card-accent-green",
    pro: "card-accent-pro",
};

function createTextEl(tag, className, text) {
    const el = document.createElement(tag);
    if (className) el.className = className;
    el.textContent = text;
    return el;
}

function createCard(card, titleLevel) {
    const article = document.createElement("article");
    article.className = "card";
    if (card.accent && ACCENT_CLASS[card.accent]) {
        article.classList.add(ACCENT_CLASS[card.accent]);
    }

    const icon = document.createElement("span");
    icon.className = "card-icon";
    icon.setAttribute("aria-hidden", "true");
    icon.textContent = card.icon;

    const title = createTextEl(`h${titleLevel}`, "card-title", card.title);
    const text = createTextEl("p", "card-text", card.text);

    article.append(icon, title, text);
    return article;
}

function createSection(section) {
    const el = document.createElement("section");
    el.className = section.sectionClass;
    if (section.id) el.id = section.id;
    if (section.titleId) {
        el.setAttribute("aria-labelledby", section.titleId);
    }

    const header = document.createElement("header");
    header.className = "section-header";

    header.appendChild(createTextEl("p", "section-eyebrow", section.eyebrow));
    const title = createTextEl(
        `h${section.titleLevel}`,
        "section-title",
        section.title
    );
    if (section.titleId) title.id = section.titleId;
    header.appendChild(title);
    header.appendChild(createTextEl("p", "section-lead", section.lead));

    const grid = document.createElement("div");
    grid.className = "card-grid";
    const cardLevel = section.cardTitleLevel || 3;
    section.cards.forEach((card) => {
        grid.appendChild(createCard(card, cardLevel));
    });

    el.append(header, grid);
    return el;
}

function showSectionsError(root, message) {
    root.innerHTML = "";
    const p = document.createElement("p");
    p.className = "empty-state";
    p.textContent = message;
    root.appendChild(p);
}

async function renderHomeSections() {
    const root = document.getElementById("sections-root");
    if (!root) return;

    try {
        const response = await fetch("data/home-sections.json");
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const data = await response.json();
        root.replaceChildren();
        data.sections.forEach((section) => {
            root.appendChild(createSection(section));
        });
    } catch (err) {
        console.error("No se pudieron cargar las secciones:", err);
        showSectionsError(
            root,
            "No se pudo cargar el contenido. Revisá que el sitio se abra desde el servidor (por ejemplo AppServ) y que exista data/home-sections.json."
        );
    }
}

document.addEventListener("DOMContentLoaded", renderHomeSections);
