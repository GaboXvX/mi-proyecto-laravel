@extends('layouts.app')

@section('content')
    <style>
        .card-header h2 {
            font-weight: 600;
            font-size: 1.5rem;
        }

        .badge {
            padding: 0.4em 0.8em;
            font-size: 0.85rem;
            border-radius: 0.5rem;
        }

        .bg-section {
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
        }

        .section-title {
            border-left: 4px solid #007bff;
            padding-left: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
            font-size: 1.25rem;
            color: #343a40;
        }

        .label-bold {
            font-weight: 600;
            color: #495057;
        }

        .timeline {
            position: relative;
            padding-left: 50px;
            list-style: none;
        }

        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
            left: 25px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-badge {
            position: absolute;
            width: 12px;
            height: 12px;
            left: 19px;
            border-radius: 50%;
            border: 2px solid white;
            background-color: #007bff;
            box-shadow: 0 0 0 4px #fff;
        }

        .timeline-panel {
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .timeline-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .info-block {
            padding: 0.5rem 0;
        }

        .user-icon {
            font-size: 2.5rem;
            color: #6c757d;
        }

        .address-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .address-item {
            margin-bottom: 8px;
        }

        .address-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
            display: inline-block;
        }

        .address-value {
            color: #2d5c8f;
        }
    </style>

    <div class="table-container py-4">
        <div>
            <div class="card-header">
                <h2 class="mb-3"><i class="fas fa-ticket-alt"></i> Detalles de la Incidencia</h2>
            </div>

            <div class="card-body">

            <div class="accordion" id="acordeonPrincipal">

                <!-- Información Básica -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingBasica">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasica" aria-expanded="true" aria-controls="collapseBasica">
                            <i class="fas fa-info-circle me-2"></i> Información Básica
                        </button>
                    </h2>
                    <div id="collapseBasica" class="accordion-collapse collapse show" aria-labelledby="headingBasica" data-bs-parent="#acordeonPrincipal">
                        <div class="accordion-body bg-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><span class="label-bold">Código:</span> {{ $incidencia->cod_incidencia }}</p>
                                    <p><span class="label-bold">Tipo:</span> {{ $incidencia->tipoIncidencia->nombre }}</p>
                                    <p><span class="label-bold">Fecha creación:</span> {{ $incidencia->created_at->setTimezone('America/Caracas')->format('d/m/Y h:i A') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><span class="label-bold">Estado:</span>
                                        <span class="badge" style="background-color: {{ $incidencia->estadoIncidencia->color ?? '#6c757d' }}; color: white;">
                                            {{ $incidencia->estadoIncidencia->nombre ?? 'N/A' }}
                                        </span>
                                    </p>
                                    <p><span class="label-bold">Prioridad:</span>
                                        <span class="badge" style="background-color: {{ $incidencia->nivelIncidencia->color ?? '#6c757d' }}; color: white;">
                                            {{ $incidencia->nivelIncidencia->nombre ?? 'N/A' }}
                                        </span>
                                    </p>
                                    <p><span class="label-bold">Vencimiento:</span>
                                        {{ $incidencia->fecha_vencimiento ? $incidencia->fecha_vencimiento->setTimezone('America/Caracas')->format('d/m/Y h:i A') : 'Sin fecha' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Imágenes de la incidencia (Antes) -->
                            <hr>
                            <h5 class="mt-4"><i class="fas fa-camera me-2"></i> Imágenes de la Incidencia (Antes)</h5>
                            <div class="row mt-3">
                                @php
                                    $fotosAntes = $incidencia->pruebasFotograficas ? $incidencia->pruebasFotograficas : collect();
                                @endphp
                                @forelse($fotosAntes as $foto)
                                    @if($foto->ruta && file_exists(public_path('storage/' . $foto->ruta)))
                                        <div class="col-md-4 mb-3">
                                            <img src="{{ asset('storage/' . $foto->ruta) }}"
                                                class="img-fluid rounded shadow"
                                                alt="Imagen incidencia"
                                                style="max-height: 250px;">
                                            @if($foto->observacion)
                                                <div class="mt-2 small text-muted">{{ $foto->observacion }}</div>
                                            @endif
                                        </div>
                                    @endif
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted"><em>No hay imágenes adjuntas</em></p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ubicación -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingUbicacion">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUbicacion" aria-expanded="false" aria-controls="collapseUbicacion">
                            <i class="fas fa-map-marker-alt me-2"></i> Ubicación
                        </button>
                    </h2>
                    <div id="collapseUbicacion" class="accordion-collapse collapse" aria-labelledby="headingUbicacion" data-bs-parent="#acordeonPrincipal">
                        <div class="accordion-body bg-section">
                            <p><span class="label-bold">Institución:</span> {{ $incidencia->institucion->nombre ?? 'N/A' }}</p>
                            <p><span class="label-bold">Unidad:</span> {{ $incidencia->estacion->nombre ?? 'N/A' }}</p>
                            <p><span class="label-bold">Municipio:</span> {{ $incidencia->estacion->municipio->nombre ?? 'N/A' }}</p>

                            <div class="address-card mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="address-item"><span class="address-label">Estado:</span> <span
                                                class="address-value">{{ $incidencia->direccionIncidencia->estado->nombre ?? 'N/A' }}</span>
                                        </div>
                                        <div class="address-item"><span class="address-label">Municipio:</span> <span
                                                class="address-value">{{ $incidencia->direccionIncidencia->municipio->nombre ?? 'N/A' }}</span>
                                        </div>
                                        <div class="address-item"><span class="address-label">Parroquia:</span> <span
                                                class="address-value">{{ $incidencia->direccionIncidencia->parroquia->nombre ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="address-item"><span class="address-label">Urbanización:</span> <span
                                                class="address-value">{{ $incidencia->direccionIncidencia->urbanizacion->nombre ?? 'N/A' }}</span>
                                        </div>
                                        <div class="address-item"><span class="address-label">Sector:</span> <span
                                                class="address-value">{{ $incidencia->direccionIncidencia->sector->nombre ?? 'N/A' }}</span>
                                        </div>
                                        <div class="address-item"><span class="address-label">Comunidad:</span> <span
                                                class="address-value">{{ $incidencia->direccionIncidencia->comunidad->nombre ?? 'N/A' }}</span>
                                        </div>
                                        <div class="address-item"><span class="address-label">Calle:</span> <span
                                                class="address-value">{{ $incidencia->direccionIncidencia->calle ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <span class="address-label">Punto de Referencia:</span>
                                    <span class="address-value">{{ $incidencia->direccionIncidencia->punto_de_referencia ?? 'No especificado' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instituciones de Apoyo -->
                @if ($incidencia->institucionesApoyo && $incidencia->institucionesApoyo->count() > 0)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingApoyo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseApoyo" aria-expanded="false" aria-controls="collapseApoyo">
                                <i class="fas fa-hands-helping me-2"></i> Instituciones de Apoyo
                            </button>
                        </h2>
                        <div id="collapseApoyo" class="accordion-collapse collapse" aria-labelledby="headingApoyo" data-bs-parent="#acordeonPrincipal">
                            <div class="accordion-body bg-section">
                                <div class="row">
                                    @foreach ($incidencia->institucionesApoyo as $apoyo)
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        {{ $apoyo->institucion->nombre ?? 'Institución no especificada' }}</h5>
                                                    @if ($apoyo->Estacion)
                                                        <p class="card-text">
                                                            <span class="label-bold">Estación:</span>
                                                            {{ $apoyo->Estacion->nombre }}
                                                        </p>
                                                    @endif
                                                    @if ($apoyo->institucion && $apoyo->institucion->municipio)
                                                        <p class="card-text">
                                                            <span class="label-bold">Ubicación:</span>
                                                            {{ $apoyo->institucion->municipio->nombre }},
                                                            {{ $apoyo->institucion->estado->nombre }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Descripción -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingDescripcion">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                            <i class="fas fa-align-left me-2"></i> Descripción
                        </button>
                    </h2>
                    <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#acordeonPrincipal">
                        <div class="accordion-body bg-section">
                            <div class="p-3 border rounded">
                                {{ $incidencia->descripcion }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reportado por -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingReporte">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReporte" aria-expanded="false" aria-controls="collapseReporte">
                            <i class="fas fa-user-tie me-2"></i> Reportado por / Persona Relacionada
                        </button>
                    </h2>
                    <div id="collapseReporte" class="accordion-collapse collapse" aria-labelledby="headingReporte" data-bs-parent="#acordeonPrincipal">
                        <div class="accordion-body bg-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user-tie me-2"></i> Reportado por</h5>
                                    @if ($incidencia->usuario && $incidencia->usuario->empleadoAutorizado)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle user-icon me-3"></i>
                                            <div>
                                                <p><strong>Nombre:</strong> {{ $incidencia->usuario->empleadoAutorizado->nombre }}
                                                    {{ $incidencia->usuario->empleadoAutorizado->apellido }}</p>
                                                <p><strong>Cédula:</strong> V-{{ $incidencia->usuario->empleadoAutorizado->cedula }}</p>
                                                <p><strong>Teléfono:</strong> {{ $incidencia->usuario->empleadoAutorizado->telefono ?? 'N/A' }}</p>
                                                <p><strong>Género:</strong> {{ $incidencia->usuario->empleadoAutorizado->genero ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted"><em>Información no disponible</em></p>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <h5><i class="fas fa-user me-2"></i> Persona Relacionada</h5>
                                    @if ($incidencia->persona)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle user-icon me-3"></i>
                                            <div>
                                                <p><strong>Nombre:</strong> {{ $incidencia->persona->nombre }}
                                                    {{ $incidencia->persona->apellido }}</p>
                                                <p><strong>Cédula:</strong> {{ $incidencia->persona->cedula }}</p>
                                                <p><strong>Teléfono:</strong> {{ $incidencia->persona->telefono ?? 'N/A' }}</p>
                                                <p><strong>Género:</strong> {{ $incidencia->persona->genero ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted"><em>No hay persona relacionada</em></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reparación (si atendida) -->
                @if (isset($reparacion) &&
                        $incidencia->estadoIncidencia &&
                        strtolower($incidencia->estadoIncidencia->nombre) == 'atendido' &&
                        $reparacion)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingReparacion">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReparacion" aria-expanded="false" aria-controls="collapseReparacion">
                                <i class="fas fa-tools me-2"></i> Detalles de la Reparación
                            </button>
                        </h2>
                        <div id="collapseReparacion" class="accordion-collapse collapse" aria-labelledby="headingReparacion" data-bs-parent="#acordeonPrincipal">
                            <div class="accordion-body bg-section">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Fecha de atención:</strong> {{ $reparacion->created_at->setTimezone('America/Caracas')->format('d/m/Y h:i A') }}
                                        </p>
                                        <p><strong>Descripción:</strong></p>
                                        <div class="p-3 rounded mb-3">
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

                                <!-- Imágenes de la reparación -->
                                <div class="mt-3">
                                    <h5><i class="fas fa-camera"></i> Pruebas Fotográficas de la Reparación</h5>
                                    <div class="row">
                                        @php
                                            $fotosDespues = $reparacion->pruebasFotograficas ? $reparacion->pruebasFotograficas : collect();
                                        @endphp
                                        @forelse($fotosDespues as $foto)
                                        @if(isset($foto->ruta))
                                            <div class="col-md-4 mb-3">
                                                <img src="{{ asset('storage/' . $foto->ruta) }}" class="img-fluid rounded shadow"
                                                    alt="Prueba fotográfica" style="max-height: 250px;">
                                                    @if($foto->observacion)
                                                    <div class="mt-2 small text-muted">{{ $foto->observacion }}</div>
                                                @endif
                                            </div>
                                            @endif
                                        @empty
                                            <div class="col-12">
                                                <p class="text-muted"><em>No hay imágenes adjuntas</em></p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Esta incidencia aún no ha sido atendida.
                    </div>
                    @endif

                <!-- Historial -->
                @if ($incidencia->movimiento && $incidencia->movimiento->count() > 0)
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistorial" aria-expanded="false" aria-controls="collapseHistorial">
                                <i class="fas fa-history me-2"></i> Historial de Movimientos
                            </button>
                        </h2>
                        <div id="collapseHistorial" class="accordion-collapse collapse" aria-labelledby="headingHistorial" data-bs-parent="#acordeonPrincipal">
                            <div class="accordion-body bg-section">
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
                        </div>
                    </div>
                @endif

            </div> <!-- fin del accordion -->

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a la lista
                </a>
                @can('descargar detalles incidencias')
                <a href="{{ route('incidencias.download', $incidencia->id_incidencia) }}" class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
                @endcan
                @if ($incidencia->estadoIncidencia && strtolower($incidencia->estadoIncidencia->nombre) != 'atendido')
                    <a href="{{ route('incidencias.atender.vista', $incidencia->slug) }}" class="btn btn-primary">
                        <i class="fas fa-tools"></i> Atender Incidencia
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
