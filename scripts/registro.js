bindPasswordToggle(
    document.getElementById("toggle-password"),
    document.getElementById("reg-password")
);
bindPasswordToggle(
    document.getElementById("toggle-password-confirm"),
    document.getElementById("reg-password-confirm")
);

const form = document.getElementById("registro-form");
const password = document.getElementById("reg-password");
const confirm = document.getElementById("reg-password-confirm");
const matchHint = document.getElementById("password-match-hint");
const avatarInput = document.getElementById("reg-avatar");
const avatarPreview = document.getElementById("avatar-preview");

function updatePasswordMatch() {
    if (!password || !confirm || !matchHint) return;
    if (!confirm.value) {
        matchHint.textContent = "";
        matchHint.classList.remove("is-error", "is-ok");
        return;
    }
    const ok = password.value === confirm.value;
    matchHint.textContent = ok ? "Las contraseñas coinciden." : "Las contraseñas no coinciden.";
    matchHint.classList.toggle("is-ok", ok);
    matchHint.classList.toggle("is-error", !ok);
}

password?.addEventListener("input", updatePasswordMatch);
confirm?.addEventListener("input", updatePasswordMatch);

if (avatarInput && avatarPreview) {
    avatarInput.addEventListener("change", () => {
        const file = avatarInput.files?.[0];
        if (!file || !file.type.startsWith("image/")) {
            avatarPreview.innerHTML =
                '<span class="avatar-preview__placeholder">Tu foto</span>';
            avatarPreview.classList.remove("has-image");
            return;
        }
        const url = URL.createObjectURL(file);
        const img = document.createElement("img");
        img.src = url;
        img.alt = "Vista previa de tu imagen de perfil";
        avatarPreview.replaceChildren(img);
        avatarPreview.classList.add("has-image");
    });
}

if (form) {
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        if (password && confirm && password.value !== confirm.value) {
            updatePasswordMatch();
            confirm.focus();
            confirm.classList.add("auth-input--error");
            return;
        }
        confirm?.classList.remove("auth-input--error");

        const submit = form.querySelector(".auth-submit");
        submit?.classList.add("is-loading");
        window.setTimeout(() => {
            submit?.classList.remove("is-loading");
            submit?.classList.add("is-success");
            window.setTimeout(() => submit?.classList.remove("is-success"), 1200);
        }, 900);
    });
}
