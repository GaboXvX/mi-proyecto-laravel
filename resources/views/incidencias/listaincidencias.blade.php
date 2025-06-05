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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-plus" viewBox="0 0 16 16">
                <path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5"/>
                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
                </svg>
                Nueva
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
                               value="{{ request('codigo') }}" placeholder="Buscar por código" maxlength="8">
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
    <div class="d-flex justify-content-end mb-3">
        @can('descargar listado incidencias')
        <form id="generar-pdf-form" action="{{ route('incidencias.generarPDF') }}" method="POST">
            @csrf
            <input type="hidden" id="pdf-fecha-inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" id="pdf-fecha-fin" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" id="pdf-estado" name="estado" value="{{ request('estado') }}">
            <input type="hidden" id="pdf-prioridad" name="prioridad" value="{{ request('prioridad') }}">
            <input type="hidden" id="pdf-codigo" name="codigo" value="{{ request('codigo') }}">
            
            <button type="submit" class="btn btn-primary" title="Descargar PDF">
                Descargar
            </button>
        </form>
        @endcan
    </div>


    <!-- Tabla de Incidencias -->
    <div class="table-responsive">
        <table class="table table-striped align-middle" id="tabla-incidencias">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Creación</th>
                    <th style="white-space: nowrap;">Registrado por</th>
                    <th>Persona</th>
                    <th>Comunidad</th>
                    <th style="white-space: nowrap;">Tiempo restante</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="incidencias-tbody">
                <!-- El contenido será llenado por JS -->
            </tbody>
        </table>
    </div>
    <div id="ultima-actualizacion" class="last-update">Última actualización: {{ now()->format('d-m-Y H:i:s') }}</div>
</div>

<script src="{{ asset('js/incidencias.js') }}"></script>

@endsection