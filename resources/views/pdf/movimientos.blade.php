<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movimientos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Listado de Movimientos</h2>
    <p>Total de registros: {{ $movimientos->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $mov)
            <tr>
                <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @php
                        if ($mov->id_usuario_afectado) $tipo = 'Usuario';
                        elseif ($mov->id_persona) $tipo = 'Persona';
                        elseif ($mov->id_direccion) $tipo = 'Dirección';
                        elseif ($mov->id_incidencia) $tipo = 'Incidencia';
                        else $tipo = 'Sistema';
                    @endphp
                    {{ $tipo }}
                </td>
                <td>{{ $mov->descripcion }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
