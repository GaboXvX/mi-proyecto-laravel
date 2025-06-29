@extends('layouts.app')
@section('content')
    <style>
        /* Estilos personalizados */
        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background-color: light-dark(#fff, #1e293b);
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 32px;
            color: light-dark(#204a77, #f0f5fd);
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
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .details-section h3 {
            font-size: 18px;
            margin-bottom: 12px;
            color: light-dark(#204a77, #f0f5fd);
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

        .button-container {
            text-align: center;
            margin-top: 40px;
        }

        /* Nuevos estilos para nivel y estado */
        .priority-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
            margin-right: 5px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
        }

        .time-remaining {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .time-critical {
            color: #dc3545;
            font-weight: bold;
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            50% { opacity: 0.5; }
        }

        @media print {
            .button-container {
                display: none;
            }
        }
        .membrete {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .membrete img {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .membrete h3 {
    font-size: 1.05rem; /* Equivalente a ~20px (depende del tamaño base) */
    word-wrap: break-word; /* Opcional: para textos muy largos */
}

        .membrete p {
            margin: 3px 0;
            font-size: 14px;
        }
    </style>
    <main >
        <!-- Membrete de la institución propietaria -->
        @if(isset($institucionPropietaria))
            <div class="membrete">
                @if($institucionPropietaria->logo_path)
                    <img src="{{ asset('storage/' . $institucionPropietaria->logo_path) }}" alt="Logo">
                @endif
<h3>{{ ucwords($institucionPropietaria->encabezado_html) }}</h3>
                 </div>
        @endif
            <div class="header">
                <h2>Comprobante de Incidencia</h2>
                <p>Detalles de la Incidencia #{{ $incidencia->id_incidencia }}</p>
            </div>

            <div class="details-section">
                <h3>Información de la Incidencia:</h3>
                <p><strong>Código de Incidencia:</strong> {{ $incidencia->cod_incidencia }}</p>
                <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>
                <p><strong>Tipo de Incidencia:</strong> {{ $incidencia->tipoIncidencia->nombre }}</p>
                
                <!-- Mostrar Nivel de Prioridad con estilo -->
                <p><strong>Nivel de Prioridad:</strong> 
                    <span class="priority-badge" style="background-color: {{ $nivel->color }}; color: white;">
                        {{ $nivel->nombre }} (Nivel {{ $nivel->nivel }})
                    </span>
                </p>
                
                <!-- Mostrar Estado con estilo -->
                <p><strong>Estado:</strong> 
                    <span class="status-badge" style="background-color: {{ $estado->color ?? '#6c757d' }}; color: white;">
                        {{ $estado->nombre }}
                    </span>
                </p>
                
<p><strong>Fecha de Creación:</strong> {{ $incidencia->created_at->setTimezone('America/Caracas')->format('d/m/Y h:i A') }}</p>                
                <!-- Mostrar Tiempo Restante si aplica -->
                @if(!($incidencia->estadoIncidencia->nombre=='atendido') && $incidencia->fecha_vencimiento)
                    <p><strong>Tiempo Estimado:</strong> 
                        <span class="time-remaining @if(now()->diffInHours($incidencia->fecha_vencimiento) < 12) time-critical @endif">
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
                <h3>Información de la Persona Afectada:</h3>
                <p><strong>Nombre:</strong> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
                <p><strong>Cédula:</strong><strong> {{$incidencia->persona->nacionalidad}}</strong>-{{  $incidencia->persona->cedula }}</p>
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
                    <p><strong>Unidad:</strong> {{ $incidencia->institucionEstacion->nombre }}</p>
                @else
                    <p><em>No hay Unidad asignada.</em></p>
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
                    <br><strong>Unidad:</strong> {{ $institucionApoyo->Estacion->nombre }}
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endif
            <div class="footer">
    @if(isset($pie_html))
        {!! $pie_html !!}
    @else
        <p>Comprobante emitido por el Ministerio del Poder Popular para la Atención de las Aguas (Minaguas).</p>
    @endif
</div>
       

        <div class="button-container">
            <a href="{{ route('incidencias.descargar', $incidencia->slug) }}" class="btn btn-success">Descargar PDF</a>
            <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </main>

    
@endsection