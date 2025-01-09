<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Personas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
    <style>
        table {
            border-collapse: collapse;
        }

        td,
        th {
            padding: 12px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-group .btn {
            flex: 1 0 auto;
        }

    </style>
</head>

<body class="bg-light">

    <div class="container my-5">
        <h1 class="mb-4 text-center">Lista de Personas</h1>
        
        <!-- Formulario de búsqueda -->
        <form action="{{ route('personas.buscar') }}" method="POST">
            @csrf
            <input type="search" name="buscar" id="buscar" placeholder="Buscar por cédula">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <!-- Mensajes de éxito y error -->
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

        <div class="mb-3">
            <a href="{{ route('home') }}" class="btn btn-primary">Volver</a>
        </div>

        <!-- Verifica si hay personas encontradas -->
        @if (!empty($personas) && count($personas) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cédula</th>
                            <th>Líder Comunitario</th>
                            <th>Correo Electrónico</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($personas as $persona)
                            <tr>
                                <td>{{ $persona->nombre }}</td>
                                <td>{{ $persona->apellido }}</td>
                                <td>{{ $persona->cedula }}</td>
                                <td>{{ $persona->lider_comunitario ? $persona->lider_comunitario->nombre : 'No asignado' }}</td>
                                <td>{{ $persona->correo }}</td>
                                <td>{{ $persona->telefono }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('personas.show', $persona->slug) }}" class="btn btn-info btn-sm">Ver</a>                 
                                        <a href="{{ route('personas.edit', $persona->slug) }}" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="{{ route('incidencias.crear', $persona->slug) }}" class="btn btn-success btn-sm">Añadir Incidencia</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Si no se encontró ninguna persona -->
            <p class="alert alert-warning">No se encontró ninguna persona con esa cédula.</p>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
