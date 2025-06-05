<!DOCTYPE html>
<html>
<head>
    <title>Listado de Incidencias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header img {
            width: 100px;
            height: auto;
        }
        .header h1 {
            font-size: 18px;
            margin: 5px 0;
            color: #333;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: right;
            font-size: 10px;
            color: #777;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Membrete -->
    <div class="header">
        <div style="text-align: center;">
            @if(isset($logoBase64))
                <img src="{{ $logoBase64 }}" style="height: 60px; margin-bottom: 10px;"><br>
            @endif
            {!! $membrete !!}
        </div>
        <p>Listado de Incidencias</p>
        <p>Fecha de generación: {{ now()->format('d-m-Y H:i:s') }}</p>
        <p>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d-m-Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d-m-Y') }}</p>
        @if($estado != 'Todos')
            <p>Filtrado por estado: {{ $estado }}</p>
        @endif
        @if($prioridad != 'Todos')
            <p>Filtrado por prioridad: {{ $prioridad }}</p>
        @endif
    </div>

    <!-- Tabla de Incidencias -->
    <table>
        <thead>
            <tr>
                <th width="10%">Código</th>
                <th width="10%">Tipo</th>
                <th width="20%">Descripción</th>
                <th width="10%">Prioridad</th>
                <th width="10%">Estado</th>
                <th width="12%">Fecha Creación</th>
                <th width="14%">Registrado por</th>
                <th width="14%">Persona</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($incidencias as $incidencia)
                <tr>
                    <td>{{ $incidencia->cod_incidencia }}</td>
                    <td>{{ $incidencia->tipo_incidencia }}</td>
                    <td>{{ Str::limit($incidencia->descripcion, 50) }}</td>
                    <td>
                        <span style="background-color: {{ $incidencia->nivelIncidencia->color ?? '#6c757d' }}; 
                              color: white; padding: 2px 5px; border-radius: 3px; display: inline-block;">
                            {{ $incidencia->nivelIncidencia->nombre ?? 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <span style="background-color: {{ $incidencia->estadoIncidencia->color ?? '#6c757d' }}; 
                              color: white; padding: 2px 5px; border-radius: 3px; display: inline-block;">
                            {{ $incidencia->estadoIncidencia->nombre ?? 'N/A' }}
                        </span>
                    </td>
                    <td>{{ $incidencia->created_at->format('d-m-Y H:i') }}</td>
                    <td>
                        @if($incidencia->usuario && $incidencia->usuario->empleadoAutorizado)
                            {{ $incidencia->usuario->empleadoAutorizado->nombre }} {{ $incidencia->usuario->empleadoAutorizado->apellido }}
                            <br><small>V-{{ $incidencia->usuario->empleadoAutorizado->cedula }}</small>
                        @else
                            <em>No registrado</em>
                        @endif
                    </td>
                    <td>
                        @if($incidencia->persona)
                            {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}
                            <br><small>V-{{ $incidencia->persona->cedula }}</small>
                        @else
                            <em>Incidencia General</em>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No se encontraron incidencias para los filtros seleccionados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total de incidencias: {{ $incidencias->count() }}<br>
        Generado por: {{ Auth::user()->empleadoAutorizado->nombre ?? 'Sistema' }}<br>

        @isset($pie_html)
            {!! $pie_html !!}<br>
        @endisset
        <span style="color: #6c757d; font-size: 0.9em;">
            Generado el {{ now()->format('d/m/Y H:i:s') }}
        </span>
    </div>
</body>
</html>