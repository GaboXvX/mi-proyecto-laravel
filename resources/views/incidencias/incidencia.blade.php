@extends('layouts.app')
@section('content')
<style>
        /* Estilos personalizados */
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

        .button-container {
            text-align: center;
            margin-top: 40px;
        }

        .btn {
            padding: 12px 25px;
            font-size: 14px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin: 10px;
        }

        .btn-download {
            background-color: #28a745;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-download:hover {
            background-color: #218838;
        }

        .btn-back {
            background-color: #6c757d;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
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
            background-color: #f8f9fa;
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
    </style>

        <div class="container" id="comprobante-container">
            <div class="header">
                <h1>Comprobante de Incidencia</h1>
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
                
                <p><strong>Fecha de Creación:</strong> {{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
                
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
                    <p><strong>Fecha de Vencimiento:</strong> {{ $incidencia->fecha_vencimiento->format('d/m/Y H:i') }}</p>
                @endif
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

        <div class="button-container">
            <a href="{{ route('incidencias.descargar', $incidencia->slug) }}" class="btn btn-download">Descargar PDF</a>
            <a href="{{ route('incidencias.index') }}" class="btn btn-back">Volver</a>
        </div>
    </main>

@endsection