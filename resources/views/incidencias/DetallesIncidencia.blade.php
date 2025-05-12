<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incidencia {{ $incidencia->cod_incidencia }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #003366; }
        .section { margin-bottom: 15px; }
        .section-title { 
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-left: 4px solid #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        .row { display: flex; margin-bottom: 5px; }
        .col-6 { width: 50%; }
        .label-bold { font-weight: bold; min-width: 120px; display: inline-block; }
        .address-card { background-color: #f8f9fa; padding: 10px; border-radius: 5px; }
        .timeline { position: relative; padding-left: 30px; }
        .timeline:before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item { position: relative; margin-bottom: 15px; }
        .timeline-badge {
            position: absolute;
            left: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
            border: 2px solid white;
        }
        .timeline-panel {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-left: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detalles de la Incidencia</h1>
        <p>Código: {{ $incidencia->cod_incidencia }}</p>
    </div>

    <!-- Información Básica -->
    <div class="section">
        <div class="section-title">Información Básica</div>
        <div class="row">
            <div class="col-6">
                <p><span class="label-bold">Tipo:</span> {{ $incidencia->tipoIncidencia->nombre }}</p>
                <p><span class="label-bold">Fecha creación:</span> {{ $incidencia->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
            <div class="col-6">
                <p><span class="label-bold">Estado:</span>
                    <span class="badge" style="background-color: {{ $incidencia->estadoIncidencia->color ?? '#6c757d' }}">
                        {{ $incidencia->estadoIncidencia->nombre ?? 'N/A' }}
                    </span>
                </p>
                <p><span class="label-bold">Prioridad:</span>
                    <span class="badge" style="background-color: {{ $incidencia->nivelIncidencia->color ?? '#6c757d' }}">
                        {{ $incidencia->nivelIncidencia->nombre ?? 'N/A' }}
                    </span>
                </p>
                <p><span class="label-bold">Vencimiento:</span>
                    {{ $incidencia->fecha_vencimiento ? $incidencia->fecha_vencimiento->format('d/m/Y H:i:s') : 'Sin fecha' }}
                </p>
            </div>
        </div>
    </div>
 <!-- Ubicación -->
                <div class="bg-section">
                    <h4 class="section-title"><i class="fas fa-map-marker-alt me-2"></i> Ubicación</h4>
                    <p><span class="label-bold">Institución:</span> {{ $incidencia->institucion->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Estación:</span> {{ $incidencia->estacion->nombre ?? 'N/A' }}</p>
                    <p><span class="label-bold">Municipio:</span> {{ $incidencia->estacion->municipio->nombre ?? 'N/A' }}
                    </p>

                    <div class="address-card mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="address-item"><span class="address-label">Estado:</span> <span
                                        class="address-value">{{ $incidencia->direccion->estado->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="address-item"><span class="address-label">Municipio:</span> <span
                                        class="address-value">{{ $incidencia->direccion->municipio->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="address-item"><span class="address-label">Parroquia:</span> <span
                                        class="address-value">{{ $incidencia->direccion->parroquia->nombre ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="address-item"><span class="address-label">Urbanización:</span> <span
                                        class="address-value">{{ $incidencia->direccion->urbanizacion->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="address-item"><span class="address-label">Sector:</span> <span
                                        class="address-value">{{ $incidencia->direccion->sector->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="address-item"><span class="address-label">Comunidad:</span> <span
                                        class="address-value">{{ $incidencia->direccion->comunidad->nombre ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="address-label">Punto de Referencia:</span>
                            <span
                                class="address-value">{{ $incidencia->direccion->punto_de_referencia ?? 'No especificado' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="bg-section">
                    <h4 class="section-title"><i class="fas fa-align-left me-2"></i> Descripción</h4>
                    <div class="p-3 bg-light rounded">
                        {{ $incidencia->descripcion }}
                    </div>
                </div>

                <!-- Reportado por -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="bg-section">
                            <h4 class="section-title"><i class="fas fa-user-tie me-2"></i> Reportado por</h4>
                            @if ($incidencia->usuario && $incidencia->usuario->empleadoAutorizado)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle user-icon me-3"></i>
                                    <div>
                                        <p><strong>Nombre:</strong> {{ $incidencia->usuario->empleadoAutorizado->nombre }}
                                            {{ $incidencia->usuario->empleadoAutorizado->apellido }}</p>
                                        <p><strong>Cédula:</strong>
                                            V-{{ $incidencia->usuario->empleadoAutorizado->cedula }}</p>
                                        <p><strong>Teléfono:</strong>
                                            {{ $incidencia->usuario->empleadoAutorizado->telefono ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted"><em>Información no disponible</em></p>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-section">
                            <h4 class="section-title"><i class="fas fa-user me-2"></i> Persona Relacionada</h4>
                            @if ($incidencia->persona)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle user-icon me-3"></i>
                                    <div>
                                        <p><strong>Nombre:</strong> {{ $incidencia->persona->nombre }}
                                            {{ $incidencia->persona->apellido }}</p>
                                        <p><strong>Cédula:</strong> {{ $incidencia->persona->cedula }}</p>
                                        <p><strong>Teléfono:</strong> {{ $incidencia->persona->telefono ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted"><em>No hay persona relacionada</em></p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reparación (si atendida) -->
                @if ($incidencia->estadoIncidencia && strtolower($incidencia->estadoIncidencia->nombre) == 'atendido' && $reparacion)
                    <div class="bg-section">
                        <h4 class="section-title"><i class="fas fa-tools me-2"></i> Detalles de la Reparación</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Fecha de atención:</strong> {{ $reparacion->created_at->format('d/m/Y H:i:s') }}
                                </p>
                                <p><strong>Descripción:</strong></p>
                                <div class="p-3 bg-light rounded mb-3">
                                    {{ $reparacion->descripcion }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if ($reparacion->personalReparacion)
                                    <p><strong>Nombre:</strong> {{ $reparacion->personalReparacion->nombre }}
                                        {{ $reparacion->personalReparacion->apellido }}</p>
                                    <p><strong>Cédula:</strong>
                                        {{ $reparacion->personalReparacion->nacionalidad }}-{{ $reparacion->personalReparacion->cedula }}
                                    </p>
                                    <p><strong>Teléfono:</strong> {{ $reparacion->personalReparacion->telefono ?? 'N/A' }}
                                    </p>
                                    <p><strong>Institución:</strong>
                                        {{ $reparacion->personalReparacion->institucion->nombre ?? 'N/A' }}</p>
                                    <p><strong>Estación:</strong>
                                        {{ $reparacion->personalReparacion->InstitucionEstacion->nombre ?? 'N/A' }}</p>
                                @else
                                    <p class="text-muted"><em>Información no disponible</em></p>
                                @endif
                            </div>
                        </div>

                        <!-- Imagen -->
                        <div class="mt-3">
                            <h5><i class="fas fa-camera"></i> Prueba Fotográfica</h5>
                            @if ($incidencia->reparacion && $incidencia->reparacion->prueba_fotografica)
    <div class="mt-3">
        <h5><i class="fas fa-camera"></i> Prueba Fotográfica</h5>
        @if(isset($incidencia->reparacion->imageSrc))
            <img src="{{ $incidencia->reparacion->imageSrc }}" 
                 style="max-width: 100%; max-height: 300px;">
        @else
            <p>Imagen no disponible para vista previa en PDF</p>
            <p>Ruta: {{ $incidencia->reparacion->prueba_fotografica }}</p>
        @endif
    </div>
@endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Esta incidencia aún no ha sido atendida.
                    </div>
                @endif

                <!-- Historial -->
                @if ($incidencia->movimiento && $incidencia->movimiento->count() > 0)
                    <div class="bg-section">
                        <h4 class="section-title"><i class="fas fa-history me-2"></i> Historial de Movimientos</h4>
                        <div class="timeline">
                            @foreach ($incidencia->movimiento as $movimiento)
                                <div class="timeline-item">
                                    <div class="timeline-badge"></div>
                                    <div class="timeline-panel">
                                        <h5 class="timeline-title">{{ $movimiento->accion }}</h5>
                                        <p><small
                                                class="text-muted">{{ $movimiento->created_at->format('d/m/Y H:i:s') }}</small>
                                        </p>
                                        <p>{{ $movimiento->descripcion }}</p>
                                        @if ($movimiento->usuario)
                                            <p class="mb-0"><small>Realizado por:
                                                    {{ $movimiento->usuario->empleadoAutorizado->nombre }}
                                                    {{ $movimiento->usuario->empleadoAutorizado->apellido }}
                                                    C.I: {{ $movimiento->usuario->empleadoAutorizado->cedula }}
                                                </small></p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
    <!-- Resto del contenido PDF (Ubicación, Descripción, etc.) -->
    <!-- Adapta las secciones de tu vista original aquí -->

    <div class="footer" style="text-align: center; margin-top: 30px; font-size: 10px; color: #6c757d;">
        Generado el {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>