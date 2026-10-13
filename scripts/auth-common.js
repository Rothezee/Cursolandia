function bindPasswordToggle(toggleBtn, passwordInput) {
    if (!toggleBtn || !passwordInput) return;

    const showLabel = toggleBtn.querySelector(".auth-toggle-pw__show");
    const hideLabel = toggleBtn.querySelector(".auth-toggle-pw__hide");

    toggleBtn.addEventListener("click", () => {
        const visible = passwordInput.type === "text";
        passwordInput.type = visible ? "password" : "text";
        toggleBtn.setAttribute("aria-pressed", String(!visible));
        toggleBtn.setAttribute(
            "aria-label",
            visible ? "Mostrar contraseña" : "Ocultar contraseña"
        );
        if (showLabel) showLabel.hidden = !visible;
        if (hideLabel) hideLabel.hidden = visible;
    });
}

function bindSubmitDemo(form) {
    if (!form) return;

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const submit = form.querySelector(".auth-submit");
        submit?.classList.add("is-loading");
        window.setTimeout(() => {
            submit?.classList.remove("is-loading");
            submit?.classList.add("is-success");
            window.setTimeout(() => submit?.classList.remove("is-success"), 1200);
        }, 900);
    });
}
