document.addEventListener("DOMContentLoaded", function () {
    const inputEmail = document.getElementById("email");
    const inputPassword = document.getElementById("password");
    const btnAcceder = document.getElementById("btnAcceder");
    const mensajeValidacion = document.getElementById("mensajeValidacion");
    const loginForm = document.getElementById("loginForm");

    function validarCredenciales() {
        const email = inputEmail.value.trim();
        const pass = inputPassword.value;
        const regexPass = /^(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        const emailOk = regexEmail.test(email);
        const passOk = regexPass.test(pass);

        inputEmail.classList.toggle("is-valid", emailOk);
        inputEmail.classList.toggle("is-invalid", !emailOk && email.length > 0);

        inputPassword.classList.toggle("is-valid", passOk);
        inputPassword.classList.toggle("is-invalid", !passOk && pass.length > 0);

        if (emailOk && passOk) {
            btnAcceder.disabled = false;
            mensajeValidacion.textContent = "Credenciales validas. Ya puedes acceder.";
            mensajeValidacion.className = "alert alert-success";
        } else {
            btnAcceder.disabled = true;
            mensajeValidacion.textContent = "La contrasena debe tener minimo 8 caracteres, incluir una mayuscula y un numero.";
            mensajeValidacion.className = "alert alert-danger";
        }
    }

    inputEmail.addEventListener("input", validarCredenciales);
    inputPassword.addEventListener("input", validarCredenciales);

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();
        mensajeValidacion.textContent = "Inicio de sesion validado para el prototipo.";
        mensajeValidacion.className = "alert alert-primary";
    });
});
