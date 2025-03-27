<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar Sesión - Minaguas</title>

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
            <!-- Mensajes de error o éxito -->
            @if (session('success'))
            <div class="alert alert-success" role="alert">
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
            <h2>Bienvenido al sistema de MinAguas!</h2>
            <p>Tu plataforma confiable para el manejo de recursos hídricos.</p>
        </div>
        <hr>
            <!-- Formulario de inicio de sesión -->
            <form method="POST" class="form-content" action="{{ route('login.authenticate') }}">
                @csrf
                <h3>Iniciar Sesión</h3>
                <input type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required>

                <input type="password" name="password" placeholder="Contraseña" required>

                <a href="{{ route('recuperar.ingresarCedula') }}">¿Olvidaste tu contraseña?</a>

                <button type="submit">Iniciar sesión</button>

                <p>¿No tienes una cuenta? <a href="{{ route('usuarios.create') }}">Regístrate aquí</a></p>
            </form>
    </div>

    <!-- Iconos sociales -->
    <div class="social-icons">
        <a href="#"><i class="bi bi-facebook"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-whatsapp"></i></a>
        <a href="#"><i class="bi bi-envelope"></i></a>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Ministerio del Poder Popular para la Atención de las Aguas</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
