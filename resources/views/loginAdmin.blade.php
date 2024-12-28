<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar Sesión - Administrador - Minaguas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Fondo general de la página */
        body {
            background-color: #e9f7ff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Estilo para el contenedor de login */
        .login-container {
            background-color: rgba(255, 255, 255, 0.8); /* Fondo blanco con opacidad */
            backdrop-filter: blur(10px); /* Efecto de desenfoque */
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            color: #007bff;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
            color: #007bff;
        }

        /* Estilo para los campos de entrada */
        .form-control {
            border-radius: 8px;
            border: 1px solid #007bff;
            box-shadow: none;
            margin-bottom: 20px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
        }

        /* Estilo para el botón */
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 1.1rem;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Estilo para el footer */
        .login-footer {
            margin-top: 20px;
        }

        .login-footer p {
            color: #007bff;
            font-size: 1rem;
        }

        .login-footer a {
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h1>Iniciar Sesión como Administrador</h1>

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

        <form method="POST" action="{{ route('login.authenticate.admin') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="admin@minaguas.com" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="******" required>
            </div>

            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
        </form>

        <div class="login-footer mt-3">
            <p>¿No eres un administrador? <a href="{{ route('login') }}">Inicia sesión como usuario</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
