<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Líder Comunitario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 500px;
        }
        .form-control,
        .form-select {
            font-size: 0.85rem;
            padding: 0.4rem;
        }
        .btn {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
        .mb-3 {
            margin-bottom: 0.75rem;
        }
        h1 {
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
        }
        .alert {
            font-size: 0.85rem;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-5 p-4 bg-white rounded shadow-sm">
        <h1 class="mb-4 text-center">Editar persona</h1>

        @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('personas.index') }}" class="btn btn-secondary btn-sm">Ir a la lista</a>
            <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">Volver</a>
        </div>

        <form action="{{ route('personas.update', $persona->slug) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control"
                    value="{{ old('nombre', $persona->nombre) }}" required>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" id="apellido" name="apellido" class="form-control"
                    value="{{ old('apellido', $persona->apellido) }}" required>
            </div>

            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula:</label>
                <input type="number" id="cedula" name="cedula" class="form-control" pattern="[0-9]{10>=11}"
                    value="{{ old('cedula', $persona->cedula) }}" required>
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" class="form-control"
                    value="{{ old('correo', $persona->correo) }}" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" class="form-control"
                    value="{{ old('telefono', $persona->telefono) }}" required>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select name="estado" id="estado" class="form-select" required disabled>
                    <option value="Sucre"
                        {{ old('estado', $persona->direccion ? $persona->direccion->estado : '') == 'Sucre' ? 'selected' : '' }}>
                        Sucre</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="municipio" class="form-label">Municipio:</label>
                <select name="municipio" id="municipio" class="form-select" required disabled>
                    <option value="Sucre"
                        {{ old('municipio', $persona->direccion ? $persona->direccion->municipio : '') == 'Sucre' ? 'selected' : '' }}>
                        Sucre</option>
                </select>
            </div>
            <livewire:dropdownpersona/>

            <div class="mb-3">
                <label for="calle" class="form-label">Calle:</label>
                <input type="text" id="calle" name="calle" class="form-control"
                    value="{{ old('calle', $persona->direccion ? $persona->direccion->calle : '') }}" required>
            </div>

            <div class="mb-3">
                <label for="manzana" class="form-label">Manzana:</label>
                <input type="text" id="manzana" name="manzana" class="form-control"
                    value="{{ old('manzana', $persona->direccion ? $persona->direccion->manzana : '') }}" required>
            </div>

            <div class="mb-3">
                <label for="num_casa" class="form-label">Número de Casa:</label>
                <input type="number" id="num_casa" name="num_casa" class="form-control"
                    value="{{ old('num_casa', $persona->direccion ? $persona->direccion->numero_de_casa : '') }}"
                    required min="1" step="1">
            </div>

            <button type="submit" class="btn btn-primary w-100">Actualizar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
