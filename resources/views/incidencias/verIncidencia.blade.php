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
        background-color: #f8f9fa;
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
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
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
        background-color: #e9ecef;
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
        color: #212529;
    }
</style>

<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0"><i class="fas fa-ticket-alt"></i> Detalles de la Incidencia</h2>
        </div>

        <div class="card-body">

            <!-- Información Básica -->
            <div class="bg-section">
                <h4 class="section-title"><i class="fas fa-info-circle me-2"></i> Información Básica</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><span class="label-bold">Código:</span> {{ $incidencia->cod_incidencia }}</p>
                        <p><span class="label-bold">Tipo:</span> {{ $incidencia->tipo_incidencia }}</p>
                        <p><span class="label-bold">Fecha creación:</span> {{ $incidencia->created_at->format('d/m/Y H:i:s') }}</p>
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
                        <p><span class="label-bold">Vencimiento:</span> {{ $incidencia->fecha_vencimiento ? $incidencia->fecha_vencimiento->format('d/m/Y H:i:s') : 'Sin fecha' }}</p>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="bg-section">
                <h4 class="section-title"><i class="fas fa-map-marker-alt me-2"></i> Ubicación</h4>
                <p><span class="label-bold">Institución:</span> {{ $incidencia->institucion->nombre ?? 'N/A' }}</p>
                <p><span class="label-bold">Estación:</span> {{ $incidencia->estacion->nombre ?? 'N/A' }}</p>
                <p><span class="label-bold">Municipio:</span> {{ $incidencia->estacion->municipio->nombre ?? 'N/A' }}</p>

                <div class="address-card mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="address-item"><span class="address-label">Estado:</span> <span class="address-value">{{ $incidencia->direccion->estado->nombre ?? 'N/A' }}</span></div>
                            <div class="address-item"><span class="address-label">Municipio:</span> <span class="address-value">{{ $incidencia->direccion->municipio->nombre ?? 'N/A' }}</span></div>
                            <div class="address-item"><span class="address-label">Parroquia:</span> <span class="address-value">{{ $incidencia->direccion->parroquia->nombre ?? 'N/A' }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="address-item"><span class="address-label">Urbanización:</span> <span class="address-value">{{ $incidencia->direccion->urbanizacion->nombre ?? 'N/A' }}</span></div>
                            <div class="address-item"><span class="address-label">Sector:</span> <span class="address-value">{{ $incidencia->direccion->sector->nombre ?? 'N/A' }}</span></div>
                            <div class="address-item"><span class="address-label">Comunidad:</span> <span class="address-value">{{ $incidencia->direccion->comunidad->nombre ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="address-label">Punto de Referencia:</span>
                        <span class="address-value">{{ $incidencia->direccion->punto_de_referencia ?? 'No especificado' }}</span>
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
                        @if($incidencia->usuario && $incidencia->usuario->empleadoAutorizado)
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle user-icon me-3"></i>
                                <div>
                                    <p><strong>Nombre:</strong> {{ $incidencia->usuario->empleadoAutorizado->nombre }} {{ $incidencia->usuario->empleadoAutorizado->apellido }}</p>
                                    <p><strong>Cédula:</strong> V-{{ $incidencia->usuario->empleadoAutorizado->cedula }}</p>
                                    <p><strong>Teléfono:</strong> {{ $incidencia->usuario->empleadoAutorizado->telefono ?? 'N/A' }}</p>
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
                        @if($incidencia->persona)
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle user-icon me-3"></i>
                                <div>
                                    <p><strong>Nombre:</strong> {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}</p>
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
            @if($incidencia->estadoIncidencia && strtolower($incidencia->estadoIncidencia->nombre) == 'atendido' && $reparacion)
                <div class="bg-section">
                    <h4 class="section-title"><i class="fas fa-tools me-2"></i> Detalles de la Reparación</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fecha de atención:</strong> {{ $reparacion->created_at->format('d/m/Y H:i:s') }}</p>
                            <p><strong>Descripción:</strong></p>
                            <div class="p-3 bg-light rounded mb-3">
                                {{ $reparacion->descripcion }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($reparacion->personalReparacion)
                                <p><strong>Nombre:</strong> {{ $reparacion->personalReparacion->nombre }} {{ $reparacion->personalReparacion->apellido }}</p>
                                <p><strong>Cédula:</strong> {{ $reparacion->personalReparacion->nacionalidad }}-{{ $reparacion->personalReparacion->cedula }}</p>
                                <p><strong>Teléfono:</strong> {{ $reparacion->personalReparacion->telefono ?? 'N/A' }}</p>
                                <p><strong>Institución:</strong> {{ $reparacion->personalReparacion->institucion->nombre ?? 'N/A' }}</p>
                                <p><strong>Estación:</strong> {{ $reparacion->personalReparacion->InstitucionEstacion->nombre ?? 'N/A' }}</p>
                            @else
                                <p class="text-muted"><em>Información no disponible</em></p>
                            @endif
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div class="mt-3">
                        <h5><i class="fas fa-camera"></i> Prueba Fotográfica</h5>
                        @if($reparacion->prueba_fotografica)
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $reparacion->prueba_fotografica) }}"
                                     alt="Prueba fotográfica de la reparación"
                                     class="img-fluid rounded shadow"
                                     style="max-height: 400px;">
                                <p class="mt-2"><small class="text-muted">Evidencia de la reparación realizada</small></p>
                            </div>
                        @else
                            <p class="text-muted"><em>No hay imagen adjunta</em></p>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Esta incidencia aún no ha sido atendida.
                </div>
            @endif

            <!-- Historial -->
            @if($incidencia->movimiento && $incidencia->movimiento->count() > 0)
                <div class="bg-section">
                    <h4 class="section-title"><i class="fas fa-history me-2"></i> Historial de Movimientos</h4>
                    <div class="timeline">
                        @foreach($incidencia->movimiento as $movimiento)
                            <div class="timeline-item">
                                <div class="timeline-badge"></div>
                                <div class="timeline-panel">
                                    <h5 class="timeline-title">{{ $movimiento->accion }}</h5>
                                    <p><small class="text-muted">{{ $movimiento->created_at->format('d/m/Y H:i:s') }}</small></p>
                                    <p>{{ $movimiento->descripcion }}</p>
                                    @if($movimiento->usuario)
                                        <p class="mb-0"><small>Realizado por: {{ $movimiento->usuario->empleadoAutorizado->nombre }} {{ $movimiento->usuario->empleadoAutorizado->apellido }}
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

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
            @if($incidencia->estadoIncidencia && strtolower($incidencia->estadoIncidencia->nombre) != 'atendido')
                <a href="{{ route('incidencias.atender.vista', $incidencia->slug) }}" class="btn btn-primary">
                    <i class="fas fa-tools"></i> Atender Incidencia
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
