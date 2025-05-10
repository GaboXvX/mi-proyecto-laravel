@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .status-pending { color: orange; }
    .status-resolved { color: green; }
    .alert { border-radius: 6px; font-weight: bold; }
    .alert-success { background-color: #28a745; color: white; }
    .alert-danger { background-color: #dc3545; color: white; }
    .alert-info { background-color: #17a2b8; color: white; }
    .btn-atender {
        background-color: #28a745;
        color: #fff;
        cursor: pointer;
        font-size: 12px;
        transition: background-color 0.3s;
    }
    .btn-atender:hover { background-color: #218838; color: #fff; }
    .btn-atender:disabled { background-color: #6c757d; cursor: not-allowed; }
    .last-update { font-size: 0.8rem; color: #6c757d; text-align: right; margin-top: 10px; }
    .filters-container { margin-bottom: 20px; }
    .table-container { margin-top: 20px; }
    
    /* Nuevos estilos para badges */
    .priority-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        color: white;
        display: inline-block;
    }
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        color: white;
        display: inline-block;
    }
    .time-critical {
        color: #dc3545;
        font-weight: bold;
    }
    .time-warning {
        color: #ffc107;
        font-weight: bold;
    }
</style>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="table-container">
    <!-- Título y Botón de Registro -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Lista de Incidencias</h2>
        <div>
            <a href="{{ route('incidencias.create') }}" class="btn btn-success" title="Registrar incidencia">
                <i class="bi bi-file-earmark-plus"></i> 
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-container">
        <div class="card-body">
            <form id="filtros-form" method="GET" action="{{ route('incidencias.index') }}">
                <div class="row">
                    <!-- Código -->
                    <div class="col-md-3">
                        <label for="codigo-busqueda" class="form-label">Código:</label>
                        <input type="text" id="codigo-busqueda" name="codigo" class="form-control"
                               value="{{ request('codigo') }}" placeholder="Buscar por código">
                    </div>
            
                    <!-- Estado -->
                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="Todos">Todos</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->nombre }}" {{ request('estado') == $estado->nombre ? 'selected' : '' }}>
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- Prioridad -->
                    <div class="col-md-3">
                        <label for="prioridad" class="form-label">Prioridad:</label>
                        <select class="form-select" id="prioridad" name="prioridad">
                            <option value="Todos">Todos</option>
                            @foreach($niveles as $nivel)
                                <option value="{{ $nivel->nombre }}" {{ request('prioridad') == $nivel->nombre ? 'selected' : '' }}>
                                    {{ $nivel->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- Rango de fechas -->
                    <div class="col-md-3">
                        <label class="form-label">Rango de Fechas:</label>
                        <div class="input-group">
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control"
                                   value="{{ request('fecha_inicio') }}">
                            <span class="input-group-text">a</span>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control"
                                   value="{{ request('fecha_fin') }}">
                        </div>
                    </div>
                </div>
               
            </form>
            
        </div>
    </div>

    <!-- Botón para Generar PDF -->
    <!-- Botón para Generar PDF -->
<div class="d-flex justify-content-end mb-3">
    @can('descargar listado incidencias')
    <form id="generar-pdf-form" action="{{ route('incidencias.generarPDF') }}" method="POST">
        @csrf
        <input type="hidden" id="pdf-fecha-inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
        <input type="hidden" id="pdf-fecha-fin" name="fecha_fin" value="{{ request('fecha_fin') }}">
        <input type="hidden" id="pdf-estado" name="estado" value="{{ request('estado') }}">
        <input type="hidden" id="pdf-prioridad" name="prioridad" value="{{ request('prioridad') }}">
        <input type="hidden" id="pdf-codigo" name="codigo" value="{{ request('codigo') }}">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf"></i> Generar PDF
        </button>
    </form>
    @endcan
</div>


    <!-- Tabla de Incidencias -->
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Creación</th>
                    <th>Registrado por</th>
                    <th>Persona</th>
                    <th>Tiempo restante</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="incidencias-tbody">
                @foreach ($incidencias as $incidencia)
                    <tr data-incidencia-id="{{ $incidencia->slug }}">
                        <td>{{ $incidencia->cod_incidencia }}</td>
                        <td>{{ $incidencia->tipoIncidencia->nombre }}</td>
                        <td>{{ Str::limit($incidencia->descripcion, 50) }}</td>
                        
                        <!-- Columna de Prioridad -->
                        <td>
                            <span class="priority-badge" style="background-color: {{ $incidencia->nivelIncidencia->color ?? '#6c757d' }}">
                                {{ $incidencia->nivelIncidencia->nombre ?? 'N/A' }}
                            </span>
                        </td>
                        
                        <!-- Columna de Estado -->
                        <td>
                            <span class="status-badge" style="background-color: {{ $incidencia->estadoIncidencia->color ?? '#6c757d' }}">
                                {{ $incidencia->estadoIncidencia->nombre ?? 'N/A' }}
                            </span>
                        </td>
                        
                        <td>{{ $incidencia->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            @if($incidencia->usuario && $incidencia->usuario->empleadoAutorizado)
                                {{ $incidencia->usuario->empleadoAutorizado->nombre }} {{ $incidencia->usuario->empleadoAutorizado->apellido }}
                                <strong>V-</strong>{{ $incidencia->usuario->empleadoAutorizado->cedula }}
                            @else
                                <em>No registrado</em>
                            @endif
                        </td>
                        <td>
                            @if($incidencia->persona)
                                {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}
                                <strong>V-</strong>{{ $incidencia->persona->cedula }}
                            @else
                                <em>Incidencia General</em>
                            @endif
                        </td>
                        
                        <!-- Columna de Tiempo Restante -->
                        <td>
                           {{$incidencia->fecha_vencimiento ?? 'N/A'}} 
                        </td>
                        
                        <!-- Columna de Acciones -->
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('incidencias.ver', $incidencia->slug) }}">
                                            <i class="bi bi-eye me-2"></i>Ver
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('incidencias.descargar', $incidencia->slug) }}">
                                            <i class="bi bi-download me-2"></i>Descargar
                                        </a>
                                    </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('incidencias.atender.vista', $incidencia->slug) }}">
                                                <i class="bi bi-check-circle me-2"></i>Atender
                                            </a>
                                        </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('incidencias.edit', $incidencia->slug) }}">
                                            <i class="bi bi-pencil-square me-2"></i>Modificar
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div id="ultima-actualizacion" class="last-update">Última actualización: {{ now()->format('d-m-Y H:i:s') }}</div>
    
    <!-- Paginación -->
    @if($incidencias instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center mt-3">
            {{ $incidencias->links() }}
        </div>
    @endif
</div>

<script src="{{ asset('js/incidencias.js') }}"></script>
@endsection