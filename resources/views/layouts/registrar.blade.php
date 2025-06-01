<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minaguas - Register</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    

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
    const paso1 = document.getElementById("step1");
    const paso2 = document.getElementById("step2");

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
        } else if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) {
            mensaje = "Debe contener al menos un símbolo especial.";
        }

        if (mensaje) {
            errorDiv.textContent = mensaje;
            errorDiv.style.display = "block";
            return false;
        } else {
            errorDiv.textContent = "";
            errorDiv.style.display = "none";
            return true;
        }
    }

    function validarYAvanzar() {
        const contraseñaValida = validarPassword();
        if (!contraseñaValida) {
            // No avanzar si la contraseña es inválida
            passwordInput.focus();
            return;
        }

        // Aquí puedes añadir otras validaciones si quieres

        // Avanzar al paso 2
        document.getElementById("step1").classList.remove("active");
        document.getElementById("step2").classList.add("active");
        document.getElementById("step1-indicator").classList.remove("active");
        document.getElementById("step2-indicator").classList.add("active");
    }

    function retrocederPaso() {
        document.getElementById("step2").classList.remove("active");
        document.getElementById("step1").classList.add("active");
        document.getElementById("step2-indicator").classList.remove("active");
        document.getElementById("step1-indicator").classList.add("active");
    }

    // Validar antes de enviar el formulario
    document.getElementById("registroForm").addEventListener("submit", function (e) {
        const contraseñaValida = validarPassword();
        if (!contraseñaValida) {
            e.preventDefault(); // Detiene el envío
            passwordInput.focus();
        }
    });
</script>

</body>
</html>