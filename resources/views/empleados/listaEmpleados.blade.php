@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Lista de Empleados Autorizados</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cargo</th>
                <th>Género</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->cedula }}</td>
                    <td>{{ $empleado->nombre }}</td>
                    <td>{{ $empleado->apellido }}</td>
                    <td>{{ $empleado->cargo->nombre_cargo ?? '-' }}</td>
                    <td>{{ $empleado->genero }}</td>
                    <td>{{ $empleado->telefono }}</td>
                    <td>
                        @can('editar empleados')
                        <a href="{{ route('empleados.edit', $empleado->id_empleado_autorizado) }}" class="btn btn-sm btn-primary">Editar</a>
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
