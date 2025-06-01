@extends('layouts.app')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between">
        <h2>Personal de Reparación</h2>
        <a href="{{ route('personal-reparacion.create') }}" class="btn btn-primary mb-3"><i class="bi bi-plus-circle"></i> Nuevo</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Género</th>
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
                    <td>{{ $persona->genero }}</td>
                    <td>{{ $persona->institucion->nombre ?? 'N/A' }}</td>
                    <td>{{ $persona->institucionEstacion->nombre ?? 'N/A' }}</td>
                    <td>{{ $persona->nacionalidad}}-{{$persona->cedula }}</td>
                    <td>{{ $persona->telefono }}</td>
                    <td>
                        
                        <a href="{{ route('personal-reparacion.edit', $persona) }}" class="btn btn-warning btn-sm">Editar</a>
                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection