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
            <form id="filtros-form">
                @csrf
                <div class="row">
                    <!-- Filtro por Código -->
                    <div class="col-md-3">
                        <label for="codigo-busqueda" class="form-label">Código:</label>
                        <input type="text" id="codigo-busqueda" class="form-control" placeholder="Buscar por código">
                    </div>

                    <!-- Filtro por Estado -->
                    <div class="col-md-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="Todos">Todos</option>
                            <option value="Atendido">Atendido</option>
                            <option value="Por atender">Por atender</option>
                        </select>
                    </div>

                    <!-- Filtro por Fechas -->
                    <div class="col-md-6">
                        <label class="form-label">Rango de Fechas:</label>
                        <div class="input-group">
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
                            <span class="input-group-text">a</span>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Botón para Generar PDF -->
    <div class="d-flex justify-content-end mb-3">
        @can('descargar listado incidencias')
        <form id="generar-pdf-form" action="{{ route('incidencias.generarPDF') }}" method="POST" style="display: inline;">
            @csrf
            <input type="hidden" id="pdf-fecha-inicio" name="fecha_inicio">
            <input type="hidden" id="pdf-fecha-fin" name="fecha_fin">
            <input type="hidden" id="pdf-estado" name="estado">
            <input type="hidden" id="pdf-codigo" name="codigo">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-file-earmark-pdf"></i> Generar PDF
            </button>
        </form>
        @endcan
    </div>

    <!-- Tabla de Incidencias -->
    <div class="container">
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="incidencias-tbody">
                    @foreach ($incidencias as $incidencia)
                        <tr data-incidencia-id="{{ $incidencia->slug }}">
                            <td>{{ $incidencia->cod_incidencia }}</td>
                            <td>{{ $incidencia->tipo_incidencia }}</td>
                            <td>{{ $incidencia->descripcion }}</td>
                            <td>{{ $incidencia->nivel_prioridad }}</td>
                            <td class="incidencia-status {{ $incidencia->estado == 'Por atender' ? 'status-pending' : 'status-resolved' }}">
                                {{ $incidencia->estado }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($incidencia->created_at)->format('d-m-Y H:i:s') }}</td>
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
                                        @if($incidencia->estado == 'Por atender')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('incidencias.atender.vista', $incidencia->slug) }}">
                                                    <i class="bi bi-check-circle me-2"></i>Atender
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <button class="dropdown-item disabled">
                                                    <i class="bi bi-check-circle me-2"></i>Atendido
                                                </button>
                                            </li>
                                        @endif
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
        <div id="ultima-actualizacion" class="last-update">Última actualización: {{ now()->format('d-m-Y H:i:s') }}</div>
    </div>
</div>

<script src="{{ asset('js/incidencias.js') }}"></script>
@endsection