<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minaguas - Register</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>
<body>
    @yield('content')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form'); // Selecciona el formulario

            form.addEventListener('submit', function (event) {
                const usernameInput = document.querySelector('input[name="nombre_usuario"]');
                const usernameValue = usernameInput.value.trim();

                // Expresión regular para validar si el valor es un correo electrónico
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (emailRegex.test(usernameValue)) {
                    alert('El nombre de usuario no puede ser un correo electrónico.');
                    event.preventDefault(); // Evita que el formulario se envíe
                    usernameInput.focus(); // Enfoca el campo de nombre de usuario
                }
            });
        });
    </script>
    <script>
        const passwordInput = document.getElementById("password");
        const errorDiv = document.getElementById("error");

        passwordInput.addEventListener("input", validarPassword);

        function validarPassword() {
            const password = passwordInput.value;
            let mensaje = "";

            if (password.length > 16) {
            mensaje = "La contraseña no debe superar los 16 caracteres.";
            } else if (password.length < 8) {
            mensaje = "La contraseña debe tener al menos 8 caracteres.";
            } else if (!/[A-Z]/.test(password)) {
            mensaje = "Debe contener al menos una letra mayúscula.";
            } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            mensaje = "Debe contener al menos un símbolo especial.";
            }

            if (mensaje) {
            passwordInput.classList.add("error");
            errorDiv.textContent = mensaje;
            errorDiv.style.display = "block";
            } else {
            passwordInput.classList.remove("error");
            errorDiv.textContent = "";
            errorDiv.style.display = "none";
            }
        }
    </script>
</body>
</html>