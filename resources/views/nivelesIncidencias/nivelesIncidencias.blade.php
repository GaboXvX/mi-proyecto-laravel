@extends('layouts.app')

@section('content')
<div class="table-container">
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <h2 class="mb-4">Niveles de Incidencia</h2>

        <a href="{{ route('niveles-incidencia.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo
        </a>
    </div>

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

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Nivel</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Vencimiento (horas)</th>
                    <th>Color</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($niveles as $nivel)
                <tr>
                    <td>{{ $nivel->nivel }}</td>
                    <td>{{ $nivel->nombre }}</td>
                    <td>{{ $nivel->descripcion }}</td>
                    <td>{{ $nivel->horas_vencimiento }}</td>
                    <td>
                        <span class="badge" style="background-color: {{ $nivel->color }}; color: white;">
                            {{ $nivel->color }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('niveles-incidencia.toggle-status', $nivel) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm {{ $nivel->activo ? 'btn-success' : 'btn-secondary' }}">
                                {{ $nivel->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('niveles-incidencia.edit', $nivel) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>editar
                        </a>
                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection