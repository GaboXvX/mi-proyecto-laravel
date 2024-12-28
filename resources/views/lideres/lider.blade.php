

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Lider y Reportes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        
        <h2>Datos de la Lider</h2>
        <table class="table table-bordered">
            <tr>
                <th>Nombre</th>
                <td>{{ $lider->nombre }}</td>
            </tr>
            <tr>
                <th>Apellido</th>
                <td>{{ $lider->apellido }}</td>
            </tr>
            <tr>
                <th>Cédula</th>
                <td>{{ $lider->cedula }}</td>
            </tr>
            <tr>
                <th>Correo</th>
                <td>{{ $lider->correo }}</td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td>{{ $lider->telefono }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ $lider->direccion->estado }}</td>
            </tr>
            <tr>
                <th>municipio</th>
                <td>{{ $lider->direccion->municipio}}</td>
            </tr>
            <tr>
                <th>comunidad</th>
                <td>{{ $lider->direccion->comunidad }}</td>
            </tr>
            <tr>
                <th>Sector</th>
                <td>{{ $lider->direccion->sector }}</td>
            </tr>
            <tr>
                <th>Número de Casa</th>
                <td>{{ $lider->domicilio->numero_de_casa }}</td>
            </tr>
            <tr><th>Responsable</th>
            <td>{{$lider->user->nombre}}{{$lider->user->apellido}}</td>
            </tr>
            <tr>
                <th>Creado en</th>
                <td>{{ $lider->created_at }}</td>
            </tr>
        </table>

        <hr>

       
        <h3>Reportes de Incidencias</h3>
        @if($lider->incidencias->isEmpty())
        <p>No hay incidencias registradas para esta lider.</p>
    @else

        <table class="table">
            <thead>
                <tr>
                    
                    <th>Tipo de Incidencia</th>
                    <th>Descripción</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha de Creación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lider->incidencias as $incidencia)
                    <tr>
                        <td>{{ $incidencia->tipo_incidencia }}</td>
                        <td>{{ $incidencia->descripcion }}</td>
                        <td>{{ $incidencia->nivel_prioridad }}</td>
                        <td>{{ $incidencia->estado }}</td>
                        <td>{{ $incidencia->created_at}}</td>
                       <td>    <a href="{{ route('incidencias.edit', ['slug' => $incidencia->slug, 'persona_slug' => $lider->slug]) }}">Modificar incidencia</a>
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