<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Modificación de Incidencia</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }

        label {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            display: inline-block;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn-link {
            color: white;
            text-decoration: none;
        }

        .btn-link:hover {
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1>Modificar Incidencia</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('incidencias.update', $incidencia->id_incidencia) }}" method="POST">
            @csrf
            @method('PUT') 

            <input type="hidden" name="id_persona" value="{{ $incidencia->id_persona }}" class="form-control mb-3" > 

            <div class="mb-3">
                <label for="tipo_incidencia" class="form-label">Tipo de incidencia:</label>
                <select id="tipo_incidencia" name="tipo_incidencia" class="form-select" required>
                    <option value="" disabled>--Seleccione--</option>
                    <option value="agua potable" {{ old('tipo_incidencia', $incidencia->tipo_incidencia) == 'agua potable' ? 'selected' : '' }}>Agua Potable</option>
                    <option value="agua servida" {{ old('tipo_incidencia', $incidencia->tipo_incidencia) == 'servida' ? 'selected' : '' }}>Servida</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion', $incidencia->descripcion) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="nivel_prioridad" class="form-label">Nivel de Incidencia:</label>
                <select name="nivel_prioridad" id="nivel_prioridad" class="form-select" required>
                    <option value="" disabled>--Seleccione--</option>
                    <option value="1" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '1' ? 'selected' : '' }}>1</option>
                    <option value="2" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '2' ? 'selected' : '' }}>2</option>
                    <option value="3" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '3' ? 'selected' : '' }}>3</option>
                    <option value="4" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '4' ? 'selected' : '' }}>4</option>
                    <option value="5" {{ old('nivel_prioridad', $incidencia->nivel_prioridad) == '5' ? 'selected' : '' }}>5</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <input type="text" id="estado" name="estado" value="{{ old('estado', $incidencia->estado) }}" class="form-control" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>

        <div class="mt-3">
            <a href="{{ route('home') }}" class="btn btn-link">Cancelar</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
