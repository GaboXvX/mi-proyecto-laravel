<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minaguas - Register</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    

</head>
<body>
    @yield('content')
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
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
    const btnSiguiente = document.getElementById("btnSiguiente");

    const rules = {
        length: document.getElementById("rule-length"),
        uppercase: document.getElementById("rule-uppercase"),
        lowercase: document.getElementById("rule-lowercase"),
        number: document.getElementById("rule-number"),
        special: document.getElementById("rule-special")
    };

    passwordInput.addEventListener("input", function () {
        const value = passwordInput.value;

        const hasUppercase = /[A-Z]/.test(value);
        const hasLowercase = /[a-z]/.test(value);
        const hasNumber = /[0-9]/.test(value);
        const hasSpecial = /[!@#$%^&*(),.?":{}|<>_\-+=]/.test(value);
        const hasMinLength = value.length >= 8;

        toggleClass(rules.uppercase, hasUppercase);
        toggleClass(rules.lowercase, hasLowercase);
        toggleClass(rules.number, hasNumber);
        toggleClass(rules.special, hasSpecial);
        toggleClass(rules.length, hasMinLength);

        // Solo si todas las condiciones se cumplen, habilita el botón
        const isValidPassword = hasUppercase && hasLowercase && hasNumber && hasSpecial && hasMinLength;
        btnSiguiente.disabled = !isValidPassword;
    });

    function toggleClass(element, condition) {
        element.classList.toggle("valid", condition);
        element.classList.toggle("invalid", !condition);
    }
</script>

</body>
</html>