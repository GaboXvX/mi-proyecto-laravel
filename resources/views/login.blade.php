<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar Sesión - Minaguas</title>

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="icon" href="{{ asset('img/icon.webp') }}" type="image/webp">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
</head>
<style>
    .alert-success{
        margin-bottom: 8px;
        background-color: green;
        color: white;
        padding: 15px 25px;
        background-color: #00c066;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(5px); /* Efecto de desenfoque */
    }

    .alert-danger{
        margin-bottom: 8px;
        background-color: #f53b3b;
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(5px); /* Efecto de desenfoque */
    }
</style>

<body>
        <!-- Mensajes de error o éxito -->
            @if (session('success'))
            <div class="alert alert-success fade show" role="alert" id="success-alert">
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
    <div class="container">
        <div class="content">
            <h2>Sistema de gestión de incidencias de agua potable y aguas servidas!</h2>
            <p>Tu plataforma confiable para el manejo de recursos hídricos.</p>
        </div>
        <hr>
            <!-- Formulario de inicio de sesión -->
            <form method="POST" class="form-content" action="{{ route('login.authenticate') }}">
                @csrf
                <h3>Iniciar Sesión</h3>
                <input type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required>

                <div class="password-container">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required oninput="showEye()">
                    <i id="toggleIcon" class="bi bi-eye-slash toggle-password" onclick="togglePassword()" style="display: none;"></i>
                </div>

                <a href="{{ route('recuperar.ingresarCedula') }}">¿Olvidaste tu contraseña o correo electrónico?</a>

                <button type="submit">Iniciar sesión</button>

                <p>¿No tienes una cuenta? <a href="{{ route('usuarios.create') }}">Regístrate aquí</a></p>
                <p>¿tienes una peticion rechazada?<a href="{{route('renovacion.mostrar')}}">Renovar</a></p>
            </form>
    </div>

    <!-- Iconos sociales -->
    <div class="social-icons">
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
            </svg></a>
        <a href="#">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
        </svg>
        </a>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
            </svg>
        </a>
        <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
            </svg>
        </a>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Ministerio del Poder Popular para la Atención de las Aguas</p>
    </footer>

    <script src="{{ asset('js/home.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('desactivado') === '1') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Has sido desactivado',
                        text: 'Por favor consulte a un administrador para más información.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        // Limpiar el parámetro de la URL después de mostrar la alerta
                        if (window.history.replaceState) {
                            const url = new URL(window.location.href);
                            url.searchParams.delete('desactivado');
                            window.history.replaceState({}, document.title, url.pathname + url.search);
                        }
                    });
                } else {
                    alert('Has sido desactivado. Por favor consulte a un administrador para más información.');
                }
            }
        });
    </script>
    <script>
        // Espera 3 segundos y luego oculta la alerta
        setTimeout(function () {
            const alert = document.getElementById('success-alert');
            if (alert) {
                // Con Bootstrap 5: usar la clase 'fade' y luego quitarla
                alert.classList.remove('show');
                alert.classList.add('fade');

                // Después de la animación (500ms), quitarlo completamente del DOM si quieres
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000); // 3000 ms = 3 segundos
    </script>
</body>
</html>