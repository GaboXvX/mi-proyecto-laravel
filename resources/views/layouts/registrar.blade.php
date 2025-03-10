<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minaguas - Register</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <style>
    /* Estilos para hacer el formulario aún más pequeño y centrado */
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Asegura que el cuerpo ocupe toda la altura de la pantalla */
    margin: 0;
    background-color: #f4f4f4;
}


input[type="text"], input[type="email"], input[type="password"], select {
    width: 100%;
    padding: 8px; /* Relleno reducido aún más */
    margin: 6px 0; /* Menos espacio entre los campos */
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}





   </style>
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