@extends('layouts.app')
@section('content')

    <!-- Contenido -->
    <div class="table-container">
        <h2>Lista de Empleados</h2>

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

        <!-- tabla -->
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Cédula</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th>Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->empleadoAutorizado->nombre }}</td>
                            <td>{{ $usuario->empleadoAutorizado->apellido }}</td>
                            <td>{{ $usuario->empleadoAutorizado->cedula }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @if ($usuario->id_estado_usuario == 1)
                                    Aceptado
                                @elseif ($usuario->id_estado_usuario == 2)
                                    Desactivado
                                @elseif ($usuario->id_estado_usuario == 3)
                                    No Verificado
                                @elseif ($usuario->id_estado_usuario == 4)
                                    Rechazado
                                @else
                                    Desconocido
                                @endif
                            </td>
                           
                            <td>{{ $usuario->created_at }}</td>
                            <td>
                                <!-- Botón "Inspeccionar" (excluye al admin) -->
                                @unless ($usuario->hasRole('admin'))
                                @can('ver movimientos usuarios')
                                    <a href="{{ route('usuarios.movimientos', $usuario->slug) }}" 
                                       class="btn btn-warning btn-sm" 
                                       title="Ver movimientos">
                                        Inspeccionar
                                    </a>
                                    @endcan
                                @endunless

                                @can('desactivar empleados')
                                @if ($usuario->id_estado_usuario == 1)
                                    <form action="{{ route('usuarios.desactivar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm">Deshabilitar</button>
                                    </form>
                                @endif
                                @endcan

                                @can('habilitar empleados')
                                @if ($usuario->id_estado_usuario == 2)
                                    <form action="{{ route('usuarios.activar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Activar</button>
                                    </form>
                                @endif
                                @endcan

                                @can('restaurar empleados')
                                <form action="{{ route('usuarios.restaurar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Restaurar</button>
                                </form>
                                @endcan

                                @if (auth()->user()->hasRole('admin') && $usuario->hasRole('registrador'))
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownPermisos{{ $usuario->id_usuario }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Permisos
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownPermisos{{ $usuario->id_usuario }}">
                                            @foreach ($permisos as $permiso)
                                                <li>
                                                    <form action="{{ route('usuarios.togglePermiso', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="permiso" value="{{ $permiso->name }}">
                                                        <button type="submit" class="dropdown-item">
                                                            {{ $permiso->name }}
                                                            @if ($usuario->hasPermissionTo($permiso->name))
                                                                <i class="bi bi-check text-success"></i>
                                                            @else
                                                                <i class="bi bi-x text-danger"></i>
                                                            @endif
                                                        </button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

@endsection