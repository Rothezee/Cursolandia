const PLANS_URL = "../data/planes.json";

function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text != null && text !== "") node.textContent = text;
    return node;
}

function createPlanCard(plan) {
    const article = document.createElement("article");
    article.className = `plan-card card glass-panel${plan.featured ? " plan-card--featured card-accent-pro" : ""}`;
    article.setAttribute("aria-labelledby", `plan-${plan.id}-title`);

    if (plan.featured) {
        const ribbon = el("span", "plan-card__ribbon", "Recomendado");
        ribbon.setAttribute("aria-hidden", "true");
        article.appendChild(ribbon);
    }

    const head = el("header", "plan-card__head");
    head.append(
        el("h2", "plan-card__name", plan.name),
        el("p", "plan-card__tagline", plan.tagline)
    );
    const priceRow = el("p", "plan-card__price");
    priceRow.append(el("span", "plan-card__amount", plan.price), document.createTextNode(` / ${plan.period}`));
    head.appendChild(priceRow);

    const list = el("ul", "plan-card__list");
    (plan.highlights || []).forEach((item) => {
        const li = el("li", null, item);
        list.appendChild(li);
    });

    const cta = document.createElement("a");
    cta.href = plan.cta?.href || "registro.php";
    const label = plan.cta?.label || "Empezar";
    if (plan.featured) {
        cta.className = "auth-submit plan-card__cta";
        const shine = el("span", "auth-submit__shine");
        shine.setAttribute("aria-hidden", "true");
        cta.append(el("span", "auth-submit__text", label), shine);
    } else {
        cta.className = "btn-outline btn-outline--cyan plan-card__cta";
        cta.textContent = label;
    }

    if (plan.cta?.hint) {
        const hint = el("p", "plan-card__hint auth-hint", plan.cta.hint);
        article.append(head, list, cta, hint);
    } else {
        article.append(head, list, cta);
    }

    const title = head.querySelector(".plan-card__name");
    if (title) title.id = `plan-${plan.id}-title`;

    return article;
}

function createComparisonTable(rows) {
    const wrap = el("div", "glass-panel plans-compare");
    wrap.appendChild(el("h2", "plans-block__title", "Comparativa detallada"));

    const table = document.createElement("table");
    table.className = "plans-table";
    table.innerHTML = `
        <caption class="plans-table__caption">Límites y beneficios por plan</caption>
        <thead>
            <tr>
                <th scope="col">Característica</th>
                <th scope="col">Gratuito</th>
                <th scope="col">Pro</th>
            </tr>
        </thead>
    `;
    const tbody = document.createElement("tbody");
    rows.forEach((row) => {
        const tr = document.createElement("tr");
        tr.append(
            el("th", "plans-table__feature", row.feature),
            el("td", null, row.gratuito),
            el("td", "plans-table__pro", row.pro)
        );
        tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    wrap.appendChild(table);
    return wrap;
}

function createActivationBlock(block) {
    const section = el("section", "plans-activation");
    section.setAttribute("aria-labelledby", "plans-activation-title");
    section.append(
        el("h2", "plans-block__title", block.title),
        el("p", "section-lead plans-activation__lead", block.lead)
    );

    const ol = el("ol", "plans-steps");
    (block.steps || []).forEach((step, i) => {
        const li = el("li", "plans-step card");
        li.append(
            el("span", "plans-step__num", String(i + 1)),
            el("h3", "plans-step__title", step.title),
            el("p", "plans-step__text", step.text)
        );
        ol.appendChild(li);
    });
    section.appendChild(ol);
    return section;
}

function createFaq(items) {
    const section = el("section", "plans-faq");
    section.appendChild(el("h2", "plans-block__title", "Preguntas frecuentes"));
    const list = el("div", "plans-faq__list");
    items.forEach((item) => {
        const details = document.createElement("details");
        details.className = "plans-faq__item card";
        const summary = el("summary", "plans-faq__q", item.q);
        details.append(summary, el("p", "plans-faq__a", item.a));
        list.appendChild(details);
    });
    section.appendChild(list);
    return section;
}

function renderPlans(data) {
    const root = document.getElementById("planes-app");
    if (!root) return;
    root.replaceChildren();

    const header = el("header", "section-header page-planes-header");
    header.append(
        el("p", "section-eyebrow", data.eyebrow),
        el("h1", "section-title", data.title),
        el("p", "section-lead", data.lead)
    );

    const grid = el("div", "plans-grid");
    (data.plans || []).forEach((plan) => grid.appendChild(createPlanCard(plan)));

    root.append(
        header,
        grid,
        createComparisonTable(data.comparison || []),
        createActivationBlock(data.activation || {}),
        createFaq(data.faq || [])
    );
}

function showError(message) {
    const root = document.getElementById("planes-app");
    if (!root) return;
    root.innerHTML = "";
    root.appendChild(el("p", "empty-state", message));
}

document.addEventListener("DOMContentLoaded", async () => {
    try {
        const res = await fetch(PLANS_URL);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        renderPlans(data);
    } catch (err) {
        console.error(err);
        showError(
            "No se pudieron cargar los planes. Abrí la página desde AppServ (no como archivo local)."
        );
    }
});
