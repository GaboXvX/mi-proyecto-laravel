<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" />
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

        /* Estilos para badges de estado y nivel */
        .badge-priority {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
        }

        .badge-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
        }

        .time-remaining {
            font-weight: bold;
        }

        .time-critical {
            color: #dc3545;
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            50% { opacity: 0.5; }
        }
    </style>
    <title>Comprobante de Incidencia</title>
</head>

<body>
    <div class="table-container">
        <div class="header">
            <h2>Comprobante de Incidencia</h2>
            <p>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</p>
        </div>

        <div class="details-section">
            <h3>Información de la Incidencia:</h3>
            <p><strong>Código de Incidencia:</strong> {{ $incidencia->cod_incidencia }}</p>
            <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>
            <p><strong>Tipo de Incidencia:</strong> {{ $incidencia->tipoIncidencia->nombre }}</p>
            
            <!-- Nivel de Prioridad con estilo -->
            <p><strong>Nivel de Prioridad:</strong> 
                <span class="badge-priority" style="background-color: {{ $incidencia->nivelIncidencia->color }}">
                    {{ $incidencia->nivelIncidencia->nombre }} (Nivel {{ $incidencia->nivelIncidencia->nivel }})
                </span>
            </p>
            
            <!-- Estado con estilo -->
            <p><strong>Estado:</strong> 
                <span class="badge-status" style="background-color: {{ $incidencia->estadoIncidencia->color ?? '#6c757d' }}">
                    {{ $incidencia->estadoIncidencia->nombre }}
                </span>
            </p>
            
            <p><strong>Fecha de Creación:</strong> {{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
            
            <!-- Tiempo restante si aplica -->
            @if(!($incidencia->estadoIncidencia->nombre=='atendido') && $incidencia->fecha_vencimiento)
            <p><strong>Tiempo Estimado:</strong> 
                    <span class="time-remaining @if(now()->gt($incidencia->fecha_vencimiento) || now()->diffInHours($incidencia->fecha_vencimiento) < 12) time-critical @endif">
                        @if(now()->gt($incidencia->fecha_vencimiento))
                            VENCIDO
                        @else
                            {{ now()->diff($incidencia->fecha_vencimiento)->format('%d días %h horas %i minutos') }}
                        @endif
                    </span>
                </p>
                <p><strong>Fecha de Vencimiento:</strong> {{ $incidencia->fecha_vencimiento->format('d/m/Y H:i') }}</p>
            @endif
        </div>

        @if(isset($incidencia->persona))
        <div class="details-section">
            <h3>Información de la Persona Afectada:</h3>
            <p><strong>Nombre:</strong> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
            <p><strong>Cédula:</strong> {{ $incidencia->persona->cedula }}</p>
            <p><strong>Teléfono:</strong> {{ $incidencia->persona->telefono }}</p>
            <p><strong>Género:</strong> {{ $incidencia->persona->genero ?? 'N/A' }}</p>
        </div>
        @endif

        @if(isset($incidencia->direccionIncidencia))
        <div class="details-section">
            <h3>Lugar de la Incidencia:</h3>
            <p><strong>Estado:</strong> {{ $incidencia->direccionIncidencia->estado->nombre }}</p>
            <p><strong>Municipio:</strong> {{ $incidencia->direccionIncidencia->municipio->nombre }}</p>
            <p><strong>Parroquia:</strong> {{ $incidencia->direccionIncidencia->parroquia->nombre }}</p>
            <p><strong>Urbanización:</strong> {{ $incidencia->direccionIncidencia->urbanizacion->nombre }}</p>
            <p><strong>Sector:</strong> {{ $incidencia->direccionIncidencia->sector->nombre }}</p>
                            <p><strong>Comunidad:</strong> {{ $incidencia->direccionIncidencia->comunidad->nombre }}</p>

            <p><strong>Calle:</strong> {{ $incidencia->direccionIncidencia->calle }}</p>
            <p><strong>Punto de Referencia:</strong> {{ $incidencia->direccionIncidencia->punto_de_referencia }}</p>
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
  @if($incidencia->institucionesApoyo->isNotEmpty())
<div class="details-section">
    <h3>Instituciones de Apoyo:</h3>
    <ul>
        @foreach($incidencia->institucionesApoyo as $institucionApoyo)
            <li>
                <strong>Institución:</strong> {{ $institucionApoyo->institucion->nombre }}
                @if($institucionApoyo->Estacion)
                    <br><strong>Estación:</strong> {{ $institucionApoyo->Estacion->nombre }}
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endif
        <div class="footer">
            <p>Comprobante emitido por el Ministerio del Poder Popular para la Atención de las Aguas (Minaguas).</p>
        </div>
    </div>
</body>

</html>