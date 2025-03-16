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
                <th>Líder</th>
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
                    <td>{{ $incidencia->created_at->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $incidencia->persona ? $incidencia->persona->nombre . ' ' . $incidencia->persona->apellido : 'No registrado' }}</td>
                    <td>{{ $incidencia->lider ? $incidencia->lider->nombre . ' ' . $incidencia->lider->apellido : 'No asignado' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>