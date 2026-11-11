const tabs = ["info", "material", "foro"];

function mostrarTab(nombre) {
    tabs.forEach((tab) => {
        const boton = document.getElementById(`tab-${tab}`);
        const panel = document.getElementById(`panel-${tab}`);
        boton.classList.toggle("is-active", tab === nombre);
        boton.setAttribute("aria-selected", String(tab === nombre));
        panel.hidden = tab !== nombre;
    });

    const url = location.pathname + location.search + (nombre === "info" ? "" : `#${nombre}`);
    history.replaceState(null, "", url);
}

function iniciarTabs() {
    document.querySelectorAll(".curso-tab").forEach((boton) => {
        boton.addEventListener("click", () => mostrarTab(boton.dataset.tab));
    });

    document.getElementById("curso-jump-foro").addEventListener("click", () => mostrarTab("foro"));

    const hash = window.location.hash.replace("#", "");
    mostrarTab(tabs.includes(hash) ? hash : "info");
}

function iniciarFiltroForo() {
    const buscador = document.getElementById("forum-search");
    const chips = document.querySelectorAll("#filter-forum-status .filter-chip");
    const hilos = document.querySelectorAll(".forum-thread-item");
    const contador = document.getElementById("forum-count");
    let estado = "todos";

    function filtrar() {
        const texto = buscador.value.trim().toLowerCase();
        let visibles = 0;

        hilos.forEach((hilo) => {
            const coincideTexto =
                texto === "" ||
                hilo.dataset.titulo.toLowerCase().includes(texto) ||
                hilo.dataset.cuerpo.toLowerCase().includes(texto);
            const coincideEstado = estado === "todos" || hilo.dataset.estado === estado;
            const mostrar = coincideTexto && coincideEstado;

            hilo.hidden = !mostrar;
            if (mostrar) visibles++;
        });

        if (visibles === hilos.length) {
            contador.textContent = `${visibles} pregunta${visibles === 1 ? "" : "s"} en este curso`;
        } else {
            contador.textContent = `${visibles} de ${hilos.length} preguntas`;
        }
    }

    buscador.addEventListener("input", filtrar);

    chips.forEach((chip) => {
        chip.addEventListener("click", () => {
            estado = chip.dataset.status;
            chips.forEach((c) => c.classList.toggle("is-active", c === chip));
            filtrar();
        });
    });

    document.getElementById("forum-filters-reset").addEventListener("click", () => {
        buscador.value = "";
        estado = "todos";
        chips.forEach((c) => c.classList.toggle("is-active", c.dataset.status === "todos"));
        filtrar();
    });

    filtrar();
}

if (document.querySelector(".curso-tabs")) {
    iniciarTabs();
    iniciarFiltroForo();
}
