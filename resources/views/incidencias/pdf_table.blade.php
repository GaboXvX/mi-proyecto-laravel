<!DOCTYPE html>
<html>
<head>
    <title>Listado de Incidencias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Listado de Incidencias</h1>
    <p>Desde: {{ $fechaInicio }} Hasta: {{ $fechaFin }}</p>
    <table>
        <thead>
            <tr>
                <th>Código de Incidencia</th>
                <th>Tipo de Incidencia</th>
                <th>Descripción</th>
                <th>Nivel de Prioridad</th>
                <th>Estado</th>
                <th>Fecha de Creación</th>
                <th>Registrado por</th>
                <th>Representante</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($incidencias as $incidencia)
                <tr>
                    <td>{{ $incidencia->cod_incidencia }}</td>
                    <td>{{ $incidencia->tipo_incidencia }}</td>
                    <td>{{ $incidencia->descripcion }}</td>
                    <td>{{ $incidencia->nivel_prioridad }}</td>
                    <td>{{ $incidencia->estado }}</td>
                    <td>{{ \Carbon\Carbon::parse($incidencia->created_at)->format('d-m-Y H:i:s') }}</td>
                    <td>
                        @if($incidencia->usuario && $incidencia->usuario->empleadoAutorizado)
                            {{ $incidencia->usuario->empleadoAutorizado->nombre }} 
                            {{ $incidencia->usuario->empleadoAutorizado->apellido }} 
                            <strong>V-</strong>{{ $incidencia->usuario->empleadoAutorizado->cedula }}
                        @else
                            <em>Empleado autorizado no asignado</em>
                        @endif
                    </td>
                    <td>
                        @if($incidencia->tipo === 'persona')
                            @if($incidencia->categoriaExclusiva && $incidencia->categoriaExclusiva->persona)
                                {{ $incidencia->categoriaExclusiva->persona->nombre ?? 'Nombre no disponible' }} 
                                {{ $incidencia->categoriaExclusiva->persona->apellido ?? 'Apellido no disponible' }} 
                                <strong>V-</strong>{{ $incidencia->categoriaExclusiva->persona->cedula ?? 'Cédula no disponible' }}<br>
                                <strong>Categoría:</strong> {{ $incidencia->categoriaExclusiva->categoria->nombre_categoria ?? 'Categoría no disponible' }}
                            @else
                                <em>No tiene un representante asignado</em>
                            @endif
                        @else
                            <em>Incidencia General</em>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
