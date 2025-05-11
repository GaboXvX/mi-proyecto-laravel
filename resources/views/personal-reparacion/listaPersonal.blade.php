@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Personal de Reparación</h1>
    <a href="{{ route('personal-reparacion.create') }}" class="btn btn-primary mb-3">Nuevo Personal</a>
    
    <div class="table-responsive">
        <table class="table table-striped">
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