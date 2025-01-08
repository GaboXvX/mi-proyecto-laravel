<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Persona y Reportes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        
        <h2>Datos de la Persona</h2>
        <table class="table table-bordered">
            <tr>
                <th>Nombre</th>
                <td>{{ $persona->nombre }}</td>
            </tr>
            <tr>
                <th>Apellido</th>
                <td>{{ $persona->apellido }}</td>
            </tr>
            <tr>
                <th>Cédula</th>
                <td>{{ $persona->cedula }}</td>
            </tr>
            <tr>
                <th>Líder Comunitario</th>
                <td>
                    @if($persona->lider_comunitario)
                        {{ $persona->lider_comunitario->nombre }}  {{ $persona->lider_comunitario->apellido }}
                    @else
                        No asignado
                    @endif
                </td>
            </tr>
            <tr>
                <th>Correo</th>
                <td>{{ $persona->correo }}</td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td>{{ $persona->telefono }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ $persona->direccion->estado }}</td>
            </tr>
            <tr>
                <th>Municipio</th>
                <td>{{ $persona->direccion->municipio }}</td>
            </tr>
            <tr>
                <th>Comunidad</th>
                <td>{{ $persona->direccion->comunidad->nombre }}</td>
            </tr>
            <tr>
                <th>Sector</th>
                <td>{{ $persona->direccion->sector->nombre }}</td>
            </tr>
            <tr>
                <th>Número de Casa</th>
                <td>
                        {{ $persona->direccion->numero_de_casa }}
                </td>
            </tr>
            <tr>
                <th>Responsable</th>
                <td>{{ $persona->user->nombre }} {{ $persona->user->apellido }}</td>
            </tr>
            <tr>
                <th>Creado en</th>
                <td>{{ $persona->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <hr>

        <h3>Reportes de Incidencias</h3>
        @if($persona->incidencias->isEmpty())
            <p>No hay incidencias registradas para esta persona.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo de Incidencia</th>
                        <th>Descripción</th>
                        <th>Nivel de Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($persona->incidencias as $incidencia)
                        <tr>
                            <td>{{ $incidencia->tipo_incidencia }}</td>
                            <td>{{ $incidencia->descripcion }}</td>
                            <td>{{ $incidencia->nivel_prioridad }}</td>
                            <td>{{ $incidencia->estado }}</td>
                            <td>{{ $incidencia->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('incidencias.edit', ['slug' => $incidencia->slug, 'persona_slug' => $persona->slug]) }}">Modificar incidencia</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
