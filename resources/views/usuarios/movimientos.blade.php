@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Registro de Movimientos en Tiempo Real
                </h5>
                <div>
                    <span class="badge bg-white text-primary fs-6">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        <span id="last-update">Actualizado: {{ now()->format('H:i:s') }}</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="p-3 border-bottom">
                <form id="filtro-form" method="GET" action="{{ route('mis.movimientos') }}" class="row gy-2 gx-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filtro_rango" class="form-label">Filtrar por rango</label>
                        <select class="form-select" name="rango" id="filtro_rango">
                            <option value="">-- Selecciona --</option>
                            <option value="ultimos_25" {{ request('rango') == 'ultimos_25' ? 'selected' : '' }}>Últimos 25</option>
                            <option value="mes_actual" {{ request('rango') == 'mes_actual' ? 'selected' : '' }}>Este mes</option>
                            <option value="mes_pasado" {{ request('rango') == 'mes_pasado' ? 'selected' : '' }}>Mes pasado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tipo_movimiento" class="form-label">Filtrar por tipo</label>
                        <select class="form-select" name="tipo" id="tipo_movimiento">
                            <option value="">-- Todos --</option>
                            <option value="usuario" {{ request('tipo') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                            <option value="persona" {{ request('tipo') == 'persona' ? 'selected' : '' }}>Persona</option>
                            <option value="direccion" {{ request('tipo') == 'direccion' ? 'selected' : '' }}>Dirección</option>
                            <option value="incidencia" {{ request('tipo') == 'incidencia' ? 'selected' : '' }}>Incidencia</option>
                            <option value="sistema" {{ request('tipo') == 'sistema' ? 'selected' : '' }}>Sistema</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">Desde</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                    </div>
            
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label">Hasta</label>
                        <input type="date" class="form-control" name="fecha_fin" value="{{ request('fecha_fin') }}">
                    </div>
            
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i> Filtrar
                        </button>
                    </div>
            
                    <div class="col-md-3 mt-2 d-flex gap-2">
                        <a href="{{ route('movimientos.exportar', request()->all()) }}" class="btn btn-success w-100">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i> Descargar listado
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table">
                        <tr>
                            <th class="py-3" style="width: 15%">Fecha</th>
                            <th class="py-3" style="width: 15%">Tipo</th>
                            <th class="py-3" style="width: 20%">Elemento</th>
                            <th class="py-3">Descripción</th>
                            <th class="py-3" style="width: 10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="movimientos-container">
                        @foreach($movimientos as $mov)
                        <tr class="border-bottom">
                            <td class="py-3">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $mov->created_at->format('d/m/Y') }}</span>
                                    <small class="text-muted">{{ $mov->created_at->format('H:i:s') }}</small>
                                </div>
                            </td>
                            <td class="py-3">
                                @php
                                    $tipo = '';
                                    $badgeClass = 'bg-secondary';
                                    if ($mov->id_usuario_afectado) {
                                        $tipo = 'Usuario';
                                        $badgeClass = 'bg-info';
                                    } elseif ($mov->id_persona) {
                                        $tipo = 'Persona';
                                        $badgeClass = 'bg-success';
                                    } elseif ($mov->id_direccion) {
                                        $tipo = 'Dirección';
                                        $badgeClass = 'bg-warning text-dark';
                                    } elseif ($mov->id_incidencia) {
                                        $tipo = 'Incidencia';
                                        $badgeClass = 'bg-danger';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill p-2">
                                    <i class="bi 
                                        @if($tipo == 'Usuario') bi-person 
                                        @elseif($tipo == 'Persona') bi-person-badge 
                                        @elseif($tipo == 'Dirección') bi-geo-alt 
                                        @elseif($tipo == 'Incidencia') bi-exclamation-triangle 
                                        @else bi-gear @endif
                                    me-1"></i>
                                    {{ $tipo ?: 'Sistema' }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($mov->id_usuario_afectado)
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-person me-2 text-info"></i>
                                        Usuario :{{ $mov->usuario->nombre_usuario }}
                                    </span>
                                @elseif($mov->id_persona)
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-person-badge me-2 text-success"></i>
                                        Persona C.I {{ $mov->persona->cedula }}
                                    </span>
                                @elseif($mov->id_direccion)
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt me-2 text-warning"></i>
                                        Dirección #{{ $mov->id_direccion }}
                                    </span>
                                @elseif($mov->id_incidencia)
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle me-2 text-danger"></i>
                                        Incidencia #{{ $mov->incidencia->cod_incidencia }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        {{ Str::limit($mov->descripcion, 80) }}
                                        @if(strlen($mov->descripcion) > 80)
                                            <a href="#" class="text-primary ms-2" data-bs-toggle="tooltip" title="{{ $mov->descripcion }}">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <button class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="tooltip" title="Ver detalles">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Mostrando <span id="current-count">{{ $movimientos->count() }}</span> de <span id="total-count">{{ $movimientos->total() }}</span> registros
                </div>
                <div id="pagination-links">
                    {{ $movimientos->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Función para actualizar los movimientos
    function updateMovimientos() {
        $.ajax({
            url: window.location.href,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#movimientos-container').html(data.html);
                $('#pagination-links').html(data.pagination);
                $('#current-count').text(data.current_count);
                $('#total-count').text(data.total_count);
                $('#last-update').text('Actualizado: ' + new Date().toLocaleTimeString());
                
                // Reinicializar tooltips después de actualizar
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            complete: function() {
                // Programar la próxima actualización
                setTimeout(updateMovimientos, 3000);
            }
        });
    }

    // Manejar paginación AJAX
    $(document).on('click', '#pagination-links a', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                window.history.pushState(null, null, $(this).attr('href'));
                $('#movimientos-container').html(data.html);
                $('#pagination-links').html(data.pagination);
                $('#current-count').text(data.current_count);
                $('#total-count').text(data.total_count);
            }
        });
    });

    // Iniciar la actualización automática
    setTimeout(updateMovimientos, 3000);
});
</script>
@endsection