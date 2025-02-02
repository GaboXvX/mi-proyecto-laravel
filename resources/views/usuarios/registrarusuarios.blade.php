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
    <div class="container">
        <div class="content">
            <h2>Bienvenido al sistema de MinAguas!</h2>
            <p>Tu plataforma confiable para el manejo de recursos hídricos.</p>
        </div>
        <hr>
        <div class="form-content">
            <h3>Registrarse</h3>

            <!-- Mensajes de éxito o error -->
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
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

            <!-- Formulario de registro -->
            <form action="{{ route('peticiones.store') }}" method="POST">
                @csrf
                <div class="row">
                    <select name="rol" id="rol" required>
                    @foreach ($roles as $rol)
                        <option value="{{ $rol->id_rol }}" {{ old('rol') == $rol->id_rol ? 'selected' : '' }}>
                            {{ $rol->rol }}
                        </option>
                    @endforeach
                </select>
                </div>
                <div class="row">
                    
                    <input type="text" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required>
                    <input type="text" id="apellido" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}" required>
                </div>
                <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Nombre de Usuario" value="{{ old('nombre_usuario') }}" required>
                <input type="text" id="cedula" name="cedula" placeholder="Cédula" value="{{ old('cedula') }}" required>
                <input type="email" id="email" name="email" placeholder="Correo Electrónico" value="{{ old('email') }}" required>
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
                
                <input type="text" id="pregunta1" name="mascota" placeholder="¿Cuál es el nombre de tu primera mascota?" required>
                <input type="text" id="pregunta2" name="ciudad" placeholder="¿En qué ciudad naciste?" required>
                <input type="text" id="pregunta3" name="amigo" placeholder="¿Cuál es el nombre de tu mejor amigo de la infancia?" required>
                <button type="submit">Registrar Usuario</button>
            </form>
            <p>¿Ya tienes cuenta? <a href="{{route('login')}}">Iniciar Sesión</a></p>
        </div>
    </div>

    <div class="social-icons">
        <a href="#"><i class="bi bi-facebook"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-whatsapp"></i></a>
        <a href="#"><i class="bi bi-envelope"></i></a>
    </div>

    <footer>
        <p>&copy; 2024 Ministerio del Poder Popular para la Atención de las Aguas</p>
    </footer>
</body>
</html>