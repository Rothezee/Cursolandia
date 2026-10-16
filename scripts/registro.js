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
const confirmPassword = document.getElementById("reg-password-confirm");
const avatarInput = document.getElementById("reg-avatar");
const avatarPreview = document.getElementById("avatar-preview");

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

if (form && password && confirmPassword) {
    form.addEventListener("submit", (e) => {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert("Las contraseñas no coinciden.");
        }
    });
}
