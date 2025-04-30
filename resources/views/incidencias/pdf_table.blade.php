<!DOCTYPE html>
<html>
<head>
    <title>Listado de Incidencias</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px; /* Reducir tamaño de fuente */
        margin: 0;
        padding: 10px;
    }

    .pdf-container {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 6px;
        text-align: left;
        font-size: 10px;
    }

    th {
        background-color: #f2f2f2;
    }

    .membrete {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }

    .membrete img {
        height: 70px;
        margin-right: 15px;
    }

    .membrete-info {
        line-height: 1.2;
    }

    .membrete-info h2 {
        margin: 0;
        font-size: 18px;
    }

    .membrete-info p {
        margin: 2px 0;
        font-size: 11px;
    }

    .fecha-emision {
        text-align: right;
        font-size: 11px;
        margin-top: -20px;
        margin-bottom: 10px;
    }
</style>

</head>
<body>
    <div class="membrete">
        <img src="{{ asset('img/logo.png') }}"  alt="Logo">
        <div class="membrete-info">
            <h2>Ministerio del Poder Popular de Atención de las Aguas</h2>
            <p>RIF: J-12345678-9</p>
            <p>Dirección: Av. Principal, Edif. Corporativo, Caracas</p>
            <p>Tel: (0212) 123-4567 | Email: contacto@empresa.com</p>
        </div>
    </div>

    <div class="fecha-emision">
        <strong>Fecha de emisión:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}
    </div>
    <h1>Listado de Incidencias</h1>
    <p>Desde: {{ $fechaInicio }} Hasta: {{ $fechaFin }}</p>
    <div class="pdf-container">
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
</div>
</body>
</html>
