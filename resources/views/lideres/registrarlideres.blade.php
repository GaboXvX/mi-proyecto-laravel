<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Captura de Datos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <style>
        .form-control, .form-select {
            font-size: 0.9rem;
            padding: 0.5rem;
        }

        .btn {
            font-size: 0.9rem;
            padding: 0.6rem;
        }

        .alert {
            font-size: 0.9rem;
        }

        .container {
            max-width: 600px;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-5 p-4 bg-white rounded shadow-sm">
        <h1 class="mb-4 text-center">Formulario de Captura de Datos</h1>

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
            <a href="{{ route('home') }}" class="btn btn-primary">Volver</a>
        </div>

        <form action="{{ route('lideres.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" id="apellido" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
            </div>

            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula:</label>
                <input type="text" id="cedula" name="cedula" class="form-control" value="{{ old('cedula') }}" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" class="form-control" value="{{ old('correo') }}" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" class="form-control" pattern="[0-9]{11}" placeholder="Ej: 1234567890" value="{{ old('telefono') }}" required>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select name="estado" id="estado" class="form-select" required disabled>
                    <option value="Sucre" {{ old('estado') == 'Sucre' ? 'selected' : '' }}>Sucre</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="municipio" class="form-label">Municipio:</label>
                <select name="municipio" id="municipio" class="form-select" required disabled>
                    <option value="Sucre" {{ old('municipio') == 'Sucre' ? 'selected' : '' }}>Sucre</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="comunidad" class="form-label">Comunidad:</label>
                <select name="comunidad" id="comunidad" class="form-select" required>
                    <option value="">Seleccione una comunidad</option>
                    <!-- Las comunidades serán agregadas desde el script -->
                </select>
            </div>
            
            <div class="mb-3">
                <label for="sector" class="form-label">Sector:</label>
                <select name="sector" id="sector" class="form-select" required>
                    <option value="">Seleccione un sector</option>
                    <!-- Los sectores serán actualizados dinámicamente según la comunidad seleccionada -->
                </select>
            </div>

            <div class="mb-3">
                <label for="calle" class="form-label">Calle:</label>
                <input type="text" id="calle" name="calle" class="form-control" value="{{ old('calle') }}" required>
            </div>

            <div class="mb-3">
                <label for="manzana" class="form-label">Manzana:</label>
                <input type="text" id="manzana" name="manzana" class="form-control" value="{{ old('manzana') }}" required>
            </div>

            <div class="mb-3">
                <label for="num_casa" class="form-label">Número de Casa:</label>
                <input type="number" id="num_casa" name="num_casa" class="form-control" value="{{ old('num_casa') }}" required min="1" step="1">
            </div>

            <button type="submit" class="btn btn-primary w-100">Enviar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const comunidadSelect = document.getElementById('comunidad');
            const sectorSelect = document.getElementById('sector');

            // Aquí definimos un objeto con las comunidades y sus respectivos sectores.
            const comunidadesSectores = {
                'Comunidad A': ['Sector 1', 'Sector 2', 'Sector 3'],
                'Comunidad B': ['Sector 4', 'Sector 5', 'Sector 6'],
                'Comunidad C': ['Sector 7', 'Sector 8'],
                'Comunidad D': ['Sector 9', 'Sector 10']
            };

            // Llenamos el select de comunidades con las opciones disponibles
            for (let comunidad in comunidadesSectores) {
                const option = document.createElement('option');
                option.value = comunidad;
                option.textContent = comunidad;
                comunidadSelect.appendChild(option);
            }

            // Evento para actualizar los sectores cuando se cambia la comunidad
            comunidadSelect.addEventListener('change', function () {
                // Limpiar el select de sectores
                sectorSelect.innerHTML = '<option value="">Seleccione un sector</option>';

                // Obtener la comunidad seleccionada
                const comunidadSeleccionada = comunidadSelect.value;

                // Si la comunidad seleccionada tiene sectores asociados, agregarlos
                if (comunidadSeleccionada && comunidadesSectores[comunidadSeleccionada]) {
                    const sectores = comunidadesSectores[comunidadSeleccionada];

                    // Crear las opciones de los sectores
                    sectores.forEach(function (sector) {
                        const option = document.createElement('option');
                        option.value = sector;
                        option.textContent = sector;
                        sectorSelect.appendChild(option);
                    });
                }
            });
        });
    </script>
</body>

</html>
