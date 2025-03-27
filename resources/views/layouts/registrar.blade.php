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
</body>
</html>