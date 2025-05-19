@extends('layouts.app')
@section('content')

    <!-- Contenido -->
    <div class="table-container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Lista de Empleados</h2>
            <button class="btn btn-success" onclick="window.location.href='{{ route('usuarios.download.pdf') }}'">Descargar</button>
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
                                @can('ver movimientos empleados')
                                    <a href="{{ route('movimientos.registradores', $usuario->slug) }}" 
                                       class="btn btn-warning btn-sm" 
                                       title="Ver movimientos">
                                        Inspeccionar
                                    </a>
                                    @endcan
                                @endunless
                                @unless ($usuario->hasRole('admin'))
                                @can('desactivar empleados')
                                @if ($usuario->id_estado_usuario == 1)
                                    <form action="{{ route('usuarios.desactivar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" title="deshabilitar">Deshabilitar</button>
                                    </form>
                                @endif
                                @endcan
                                    @endunless
                                    @unless ($usuario->hasRole('admin'))
                                @can('habilitar empleados')
                                @if ($usuario->id_estado_usuario == 2)
                                    <form action="{{ route('usuarios.activar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Activar</button>
                                    </form>
                                @endif
                                @endcan
                                @endunless
                                @unless ($usuario->hasRole('admin'))
                              
                                <form action="{{ route('usuarios.restaurar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Restaurar</button>
                                </form>
                               
                                @endunless
                                @if (auth()->user()->hasRole('admin') && $usuario->hasRole('registrador'))
                                    <a href="{{ route('usuarios.asignarPermisos', $usuario->id_usuario) }}" 
                                       class="btn btn-info btn-sm" 
                                       title="Asignar Permisos">
                                        <i class="bi bi-shield-lock"></i> Permisos
                                    </a>
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