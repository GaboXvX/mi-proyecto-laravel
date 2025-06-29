<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Incidencia #{{ $incidencia->cod_incidencia }}</title>
    <style>
        /* Estilos personalizados */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            font-size: 24px;
            color: #204a77;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #777;
            margin-top: 0;
        }

        .details-section {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
        }

        .details-section h3 {
            font-size: 16px;
            margin: 0 0 10px 0;
            color: #204a77;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .details-section p {
            font-size: 13px;
            margin: 3px 0;
            line-height: 1.3;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
            color: #666;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }

        /* Estilos para badges */
        .priority-badge, .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            display: inline-block;
        }

        .time-remaining {
            font-size: 12px;
            padding: 3px 6px;
        }

        .time-critical {
            color: #dc3545;
        }

        .membrete {
            text-align: center;
            margin-bottom: 10px;
        }

        .membrete img {
            max-height: 60px;
            margin-bottom: 5px;
        }

         .membrete h3 {
    font-size: 1.03rem; /* Equivalente a ~20px (depende del tamaño base) */
    word-wrap: break-word; /* Opcional: para textos muy largos */
}
        .membrete p {
            margin: 2px 0;
            font-size: 12px;
        }

        /* Estilo para listas compactas */
        ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        li {
            font-size: 13px;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Membrete institucional -->
        @if(isset($institucionPropietaria))
            <div class="membrete">
                @if($institucionPropietaria->logo_path)
                    <img src="{{ storage_path('app/public/' . $institucionPropietaria->logo_path) }}" alt="Logo">
                @endif
<h3>{{ ucwords($institucionPropietaria->encabezado_html) }}</h3>            </div>
        @endif

        <div class="header">
            <h2>Comprobante de Incidencia</h2>
            <p>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</p>
        </div>

        <div class="details-section">
            <h3>Información de la Incidencia</h3>
            <p><strong>Código:</strong> {{ $incidencia->cod_incidencia }}</p>
            <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>
            <p><strong>Tipo:</strong> {{ $incidencia->tipoIncidencia->nombre }}</p>
            <p><strong>Prioridad:</strong> 
                <span class="priority-badge" style="background-color: {{ $nivel->color }}; color: white;">
                    {{ $nivel->nombre }} (Nivel {{ $nivel->nivel }})
                </span>
            </p>
            <p><strong>Estado:</strong> 
                <span class="status-badge" style="background-color: {{ $estado->color ?? '#6c757d' }}; color: white;">
                    {{ $estado->nombre }}
                </span>
            </p>
<p><strong>Fecha de Creación:</strong> {{ $incidencia->created_at->setTimezone('America/Caracas')->format('d/m/Y h:i A') }}</p>                
            @if(!($incidencia->estadoIncidencia->nombre == 'Atendido') && $incidencia->fecha_vencimiento)
                <p><strong>Tiempo Restante:</strong> 
                    <span class="time-remaining @if(now()->gt($incidencia->fecha_vencimiento) || now()->diffInHours($incidencia->fecha_vencimiento) < 12) time-critical @endif">
                        @if(now()->gt($incidencia->fecha_vencimiento))
                            VENCIDO
                        @else
                            {{ $tiempoRestante->format('%d días %h horas %i minutos') }}
                        @endif
                    </span>
                </p>
                    <p><strong>Fecha de Vencimiento:</strong> {{ $incidencia->fecha_vencimiento->setTimezone('America/Caracas')->format('d/m/Y h:i A') }}</p>
            @endif
        </div>

        @if(isset($incidencia->persona))
        <div class="details-section">
            <h3>Persona Afectada</h3>
            <p><strong>Nombre:</strong> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
            <p><strong>Cédula:</strong> <strong>{{ $incidencia->persona->nacionalidad }}</strong>-{{ $incidencia->persona->cedula }}</p>
            <p><strong>Teléfono:</strong> {{ $incidencia->persona->telefono }}</p>
            <p><strong>Género:</strong> {{ $incidencia->persona->genero ?? 'N/A' }}</p>
        </div>
        @endif

        @if(isset($incidencia->direccionIncidencia))
        <div class="details-section">
            <h3>Ubicación</h3>
            <p><strong>Estado/Municipio/Parroquia:</strong> 
                {{ $incidencia->direccionIncidencia->estado->nombre }} / 
                {{ $incidencia->direccionIncidencia->municipio->nombre }} / 
                {{ $incidencia->direccionIncidencia->parroquia->nombre }}
            </p>
            <p><strong>Zona:</strong> 
                {{ $incidencia->direccionIncidencia->urbanizacion->nombre }} / 
                {{ $incidencia->direccionIncidencia->sector->nombre }} / 
                {{ $incidencia->direccionIncidencia->comunidad->nombre }}
            </p>
            <p><strong>Calle/Referencia:</strong> 
                {{ $incidencia->direccionIncidencia->calle }} / 
                {{ $incidencia->direccionIncidencia->punto_de_referencia }}
            </p>
        </div>
        @endif

        <div class="details-section">
            <h3>Institución Responsable</h3>
            <p><strong>Institución:</strong> {{ $incidencia->institucion->nombre }}</p>
            <p><strong>Unidad:</strong> {{ $incidencia->institucionEstacion->nombre ?? 'No asignada' }}</p>
        </div>

        @if($incidencia->institucionesApoyo->isNotEmpty())
        <div class="details-section">
            <h3>Instituciones de Apoyo ({{ $incidencia->institucionesApoyo->count() }})</h3>
            @foreach($incidencia->institucionesApoyo as $institucionApoyo)
                <p>
                    <strong>{{ $institucionApoyo->institucion->nombre }}</strong>
                    @if($institucionApoyo->Estacion)
                        (Unidad: {{ $institucionApoyo->Estacion->nombre }})
                    @endif
                </p>
            @endforeach
        </div>
        @endif

        <div class="footer">
            {!! $pie_html ?? 'Comprobante emitido por el Ministerio del Poder Popular para la Atención de las Aguas (Minaguas)' !!}
        </div>
    </div>
</body>
</html>