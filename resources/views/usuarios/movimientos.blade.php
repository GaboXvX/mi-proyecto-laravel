@extends('layouts.app')

@section('content')
<div class="table-container py-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Registro de Movimientos en Tiempo Real</h2>
            <a href="{{ route('movimientos.exportar', request()->all()) }}" class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-down" viewBox="0 0 16 16">
  <path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293z"/>
  <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
</svg>
                Descargar listado
            </a>
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
            
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-3 mt-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel me-1"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table">
                        <tr>
                            <th class="py-3">Fecha</th>
                            <th class="py-3">Tipo</th>
                            <th class="py-3">Elemento</th>
                            <th class="py-3">Descripción</th>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle me-2" viewBox="0 0 16 16">
  <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
  <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
</svg>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div id="pagination-links">
                    {{ $movimientos->onEachSide(1)->links() }}
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