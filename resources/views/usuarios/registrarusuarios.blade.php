<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
        }

        .alert {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Registro de Usuario</h1>

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

        <form action="{{ route('peticiones.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select name="rol" id="rol" class="form-control">
                    @foreach ($roles as $rol)
                        <option value="{{ $rol->id_rol }}" {{ old('rol') == $rol->id_rol ? 'selected' : '' }}>
                            {{ $rol->rol }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" id="apellido" name="apellido" class="form-control" value="{{ old('apellido') }}"
                    required>
            </div>

            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula:</label>
                <input type="text" id="cedula" name="cedula" class="form-control" value="{{ old('cedula') }}" required>
            </div>

            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control"
                    value="{{ old('nombre_usuario') }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Electrónico:</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="pregunta1" class="form-label">¿Cuál es el nombre de tu primera mascota?</label>
                <input type="text" class="form-control" id="pregunta1" name="mascota" required>
            </div>
            <div class="mb-3">
                <label for="pregunta2" class="form-label">¿En qué ciudad naciste?</label>
                <input type="text" class="form-control" id="pregunta2" name="ciudad" required>
            </div>
            <div class="mb-3">
                <label for="pregunta3" class="form-label">¿Cuál es el nombre de tu mejor amigo de la infancia?</label>
                <input type="text" class="form-control" id="pregunta3" name="amigo" required>
            </div>
            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
