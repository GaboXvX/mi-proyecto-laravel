<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 32px;
            color: #003366;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 16px;
            color: #777;
            margin-top: 0;
        }

        .details-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .details-section h3 {
            font-size: 18px;
            margin-bottom: 12px;
            color: #003366;
            border-bottom: 2px solid #003366;
            padding-bottom: 8px;
        }

        .details-section p {
            font-size: 14px;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
    </style>
    <title>Comprobante de Incidencia</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Comprobante de Incidencia</h1>
            <p>Detalles de la Incidencia #{{ $incidencia->id_incidencia ?? 'N/A' }}</p>
        </div>

        <div class="details-section">
            <h3>Información de la Incidencia:</h3>
            <p><strong>Código de Incidencia:</strong> {{ $incidencia->cod_incidencia }}</p>
            <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>
            <p><strong>Tipo de Incidencia:</strong> {{ $incidencia->tipo_incidencia }}</p>
            <p><strong>Nivel de Prioridad:</strong> {{ $incidencia->nivel_prioridad }}</p>
            <p><strong>Estado:</strong> {{ $incidencia->estado }}</p>
            <p><strong>Fecha de Creación:</strong> {{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
        </div>

        @if(isset($incidencia->persona))
        <div class="details-section">
            <h3>Información de la Persona Afectada:</h3>
            <p><strong>Nombre:</strong> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
            <p><strong>Cédula:</strong> {{ $incidencia->persona->cedula }}</p>
            <p><strong>Teléfono:</strong> {{ $incidencia->persona->telefono }}</p>
        </div>
        @endif

        @if(isset($incidencia->direccion))
        <div class="details-section">
            <h3>Lugar de la Incidencia:</h3>
            <p><strong>Estado:</strong> {{ $incidencia->direccion->estado->nombre }}</p>
            <p><strong>Municipio:</strong> {{ $incidencia->direccion->municipio->nombre }}</p>
            <p><strong>Parroquia:</strong> {{ $incidencia->direccion->parroquia->nombre }}</p>
            <p><strong>Urbanización:</strong> {{ $incidencia->direccion->urbanizacion->nombre }}</p>
            <p><strong>Sector:</strong> {{ $incidencia->direccion->sector->nombre }}</p>
            <p><strong>Calle:</strong> {{ $incidencia->direccion->calle }}</p>
            <p><strong>Punto de Referencia:</strong> {{ $incidencia->direccion->punto_de_referencia }}</p>
        </div>
        @endif

        <div class="details-section">
            <h3>Institución Responsable:</h3>
            <p><strong>Institución:</strong> {{ $incidencia->institucion->nombre }}</p>
            @if(isset($incidencia->institucionEstacion))
                <p><strong>Estación:</strong> {{ $incidencia->institucionEstacion->nombre }}</p>
            @else
                <p><em>No hay estación asignada.</em></p>
            @endif
        </div>

        <div class="footer">
            <p>Comprobante emitido por el Ministerio del Poder Popular para la Atención de las Aguas (Minaguas).</p>
        </div>
    </div>
</body>

</html>
