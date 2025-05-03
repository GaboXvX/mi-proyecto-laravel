<!DOCTYPE html>
<html>
<head>
    <title>Listado de Incidencias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
            height: auto;
        }
        .header h1 {
            font-size: 18px;
            margin: 5px 0;
        }
        .header p {
            font-size: 14px;
            margin: 0;
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
    <!-- Membrete -->
    <div class="header">
        <h1>Ministerio del Poder Popular para la Atención de las Aguas</h1>
        <p>Listado de Incidencias</p>
        <p>Desde: {{ $fechaInicio }} Hasta: {{ $fechaFin }}</p>
    </div>

    <!-- Tabla de Incidencias -->
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
                <th>Persona Afectada</th>
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
                            <em>No registrado</em>
                        @endif
                    </td>
                    <td>
                        @if($incidencia->persona)
                            {{ $incidencia->persona->nombre }} 
                            {{ $incidencia->persona->apellido }} 
                            <strong>V-</strong>{{ $incidencia->persona->cedula }}
                        @else
                            <em>Incidencia General</em>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
