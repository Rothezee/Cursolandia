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
