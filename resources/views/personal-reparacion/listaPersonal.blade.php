@extends('layouts.app')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Personal de Reparación</h2>
        <div>
            <button class="btn btn-success" onclick="window.location.href='{{ route('personal.download.pdf') }}'">
                <i class="bi bi-file-earmark-arrow-down"></i> Descargar
            </button>
            <a href="{{ route('personal-reparacion.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped datatable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Institución</th>
                    <th>Estación</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($personal as $persona)
                <tr>
                    <td>{{ $persona->nombre }} </td>
                    <td>{{ $persona->apellido }}</td>
                    <td>{{ $persona->institucion->nombre ?? 'N/A' }}</td>
                    <td>{{ $persona->institucionEstacion->nombre ?? 'N/A' }}</td>
                    <td>{{ $persona->cedula }}</td>
                    <td>{{ $persona->telefono }}</td>
                    <td>
                        <a href="{{ route('personal-reparacion.show', $persona->id_personal_reparacion) }}" class="btn btn-info btn-sm">Ver</a>
                        <a href="{{ route('personal-reparacion.edit', $persona) }}" class="btn btn-warning btn-sm">Editar</a>
                      
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection