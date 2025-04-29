@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .status-pending {
        color: orange;
    }
    .status-resolved {
        color: green;
    }
    .status-closed {
        color: red;
    }
    .alert {
        border-radius: 6px;
        font-weight: bold;
    }
    .alert-success {
        background-color: #28a745;
        color: white;
    }
    .alert-danger {
        background-color: #dc3545;
        color: white;
    }
    .btn-atender {
        background-color: #28a745;
        color: #fff;
        cursor: pointer;
        font-size: 12px;
        transition: background-color 0.3s;
    }
    .btn-atender:hover {
        background-color: #218838;
        color: #fff;
    }
    .btn-atender:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
    }
</style>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="table-container">
    <div class="d-flex justify-content-between align-item-center mb-3">
        <h2>Lista de Incidencias</h2>
        <div class="gen-pdf">
            <a href="{{ route('incidencias.generales.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Registrar Incidencia General
            </a>
            
            @can('descargar listado incidencias')
            <form id="generar-pdf-form" action="{{ route('incidencias.generarPDF') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" id="pdf-fecha-inicio" name="fecha_inicio">
                <input type="hidden" id="pdf-fecha-fin" name="fecha_fin">
                <input type="hidden" id="pdf-estado" name="estado">
                <input type="hidden" id="pdf-codigo" name="codigo"> <!-- Nuevo campo para el código -->
                <button type="submit" class="btn btn-primary">Generar PDF</button>
            </form>
            @endcan
        </div>
    </div>
   
    <!-- Filters -->
    <div class="d-flex filters-container gap-2">
        <form id="busqueda-codigo-form" class="input-group input-group-sm">
            <button class="input-group-text btn btn-primary" id="basic-addon1" type="button">
                <i class="bi bi-search"></i>
            </button>
            <input type="text" id="codigo-busqueda" class="form-control form-control-sm" placeholder="Ingrese un código">
        </form>
        <form id="filtros-form">
            @csrf
            <label for="fecha_inicio" class="form-label">Selecciona el período:</label>
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex">
                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2 mb-3" />
                    <span class="m-2">hasta</span>
                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control ml-2 mb-3" />
                </div>
                <select class="form-select form-select-sm w-50 m-2" aria-label="Select status" name="estado" id="estado">
                    <option value="Todos" selected>Todos</option>
                    <option value="Atendido">Atendido</option>
                    <option value="Por atender">Por atender</option>
                </select>
            </div>
        </form>
    </div>

    <div id="resultados" class="mt-3"></div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Código de incidencia</th>
                    <th>Tipo de Incidencia</th>
                    <th>Descripción</th>
                    <th>Nivel de Prioridad</th>
                    <th>Estado</th>
                    <th>Creación</th>
                    <th>Registrado por</th>
                    <th>Representante</th>
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
                        <td class="incidencia-status 
                                    @if($incidencia->estado == 'Por atender') status-pending 
                                    @elseif($incidencia->estado == 'Atendido') status-resolved 
                                    @endif">
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
                            @if($incidencia->tipo === 'persona')
                                @if($incidencia->categoriaExclusiva && $incidencia->categoriaExclusiva->persona)
                                    {{ $incidencia->categoriaExclusiva->persona->nombre }} {{ $incidencia->categoriaExclusiva->persona->apellido }}
                                    <strong>V-</strong>{{ $incidencia->categoriaExclusiva->persona->cedula }}
                                @else
                                    <em>No tiene un representante asignado</em>
                                @endif
                            @else
                                <em>Incidencia General</em>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('incidencias.ver', $incidencia->slug) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a href="{{ route('incidencias.descargar', $incidencia->slug) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-download"></i>
                                </a>
                                @if($incidencia->estado == 'Por atender')
                                    <a href="{{ route('incidencias.atender.vista', $incidencia->slug) }}" class="btn btn-atender btn-sm">
                                        <i class="bi bi-check-circle"></i> Atender
                                    </a>
                                @else
                                    <button class="btn btn-atender btn-sm" disabled>
                                        <i class="bi bi-check-circle"></i> Atendido
                                    </button>
                                @endif
                                <a href="{{ route('incidenciaslider.edit', $incidencia->slug) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Modificar
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src=" {{ asset('js/incidencias.js') }}"></script>

@endsection