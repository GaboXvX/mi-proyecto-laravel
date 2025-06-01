<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Género</th>
            <th>Cédula</th>
            <th>Cargo</th>
           <th>Usuario</th>
            <th>Rol</th>
            <th>Correo</th>
            <th>Estado</th>
            <th>Creación</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @php
            $idsUsuarios = $usuarios->pluck('empleadoAutorizado.id_empleado_autorizado')->filter()->toArray();
        @endphp
        @foreach ($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->empleadoAutorizado->nombre ?? '-' }}</td>
                <td>{{ $usuario->empleadoAutorizado->apellido ?? '-' }}</td>
                <td>{{ $usuario->empleadoAutorizado->genero ?? '-' }}</td>
                <td>{{ $usuario->empleadoAutorizado->cedula ?? '-' }}</td>
                <td>{{ $usuario->empleadoAutorizado->cargo->nombre_cargo ?? 'No definido' }}</td>
                <td>{{ $usuario->nombre_usuario?? '-' }}</td>

                <td>
                    @if($usuario->roles && $usuario->roles->count())
                        {{ $usuario->roles->pluck('name')->implode(', ') }}
                    @else
                        <span class="text-muted">No asignado</span>
                    @endif
                </td>
                <td>{{ $usuario->email ?? 'No definido' }}</td>
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
                        @if (empty($usuario->empleadoAutorizado))
                            Sin registrarse
                        @else
                            Desconocido
                        @endif
                    @endif
                </td>
                <td>{{ $usuario->created_at ?? '-' }}</td>
                <td>
                    @if (empty($usuario->empleadoAutorizado))
                        <span class="text-muted">No disponible</span>
                    @else
                        @if($usuario->hasRole('registrador'))
                            <div class="dropdown">
                                <button class="btn btn-link text-dark p-0 m-0" type="button" id="dropdownMenuButton-{{ $usuario->id_usuario }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $usuario->id_usuario }}">
                                    @can('ver movimientos empleados')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('movimientos.registradores', $usuario->slug) }}">
                                                <i class="bi bi-eye me-2"></i>Inspeccionar
                                            </a>
                                        </li>
                                    @endcan
                                        <li>
                                            <a class="dropdown-item" href="{{ route('empleados.edit', $usuario->empleadoAutorizado->id_empleado_autorizado) }}">
                                                <i class="bi bi-pencil-square me-2"></i>Editar empleado
                                            </a>
                                        </li>
                                    @can('desactivar empleados')
                                        @if ($usuario->id_estado_usuario == 1)
                                            <li>
                                                <form action="{{ route('usuarios.desactivar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"><i class="bi bi-person-dash me-2"></i>Deshabilitar</button>
                                                </form>
                                            </li>
                                        @endif
                                    @endcan
                                    @can('habilitar empleados')
                                        @if ($usuario->id_estado_usuario == 2)
                                            <li>
                                                <form action="{{ route('usuarios.activar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"><i class="bi bi-person-check me-2"></i>Activar</button>
                                                </form>
                                            </li>
                                        @endif
                                    @endcan
                                    <li>
                                        <form action="{{ route('usuarios.restaurar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item"><i class="bi bi-arrow-clockwise me-2"></i>Restaurar</button>
                                        </form>
                                    </li>
                                    @if (auth()->user()->hasRole('admin') && $usuario->hasRole('registrador'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('usuarios.asignarPermisos', $usuario->id_usuario) }}">
                                                <i class="bi bi-shield-lock me-2"></i>Permisos
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <button type="button" class="dropdown-item btn-renovar-intentos" data-id="{{ $usuario->id_usuario }}">
                                            <i class="bi bi-arrow-repeat me-2"></i>Renovar intentos
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
        @foreach ($empleadosSinUsuario as $empleado)
            <tr>
                <td>{{ $empleado->nombre }}</td>
                <td>{{ $empleado->apellido }}</td>
                <td>{{ $empleado->genero }}</td>
                <td>{{ $empleado->cedula }}</td>
                <td>{{ $empleado->cargo->nombre_cargo ?? 'No definido' }}</td>
                <td><span class="text-muted">No definido</span></td>
                <td><span class="text-muted">No asignado</span></td>
                <td><span class="text-warning">Sin registrarse</span></td>
                                <td><span class="text-muted">No asignado</span></td>

                <td>{{ $empleado->created_at }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-link text-dark p-0 m-0" type="button" id="dropdownMenuButton-empleado-{{ $empleado->id_empleado_autorizado }}" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-empleado-{{ $empleado->id_empleado_autorizado }}">
                            @can('editar empleados')
                                <li>
                                    <a class="dropdown-item" href="{{ route('empleados.edit', $empleado->id_empleado_autorizado) }}">
                                        <i class="bi bi-pencil-square me-2"></i>Modificar
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-renovar-intentos').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Renovar intentos de recuperación?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, renovar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/usuarios/${id}/renovar-intentos`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async response => {
                        let data = await response.text();
                        try { data = JSON.parse(data); } catch { }
                        if (response.ok && data && data.success !== false) {
                            Swal.fire('¡Éxito!', data.message || 'Intentos renovados correctamente.', 'success');
                        } else {
                            Swal.fire('Error', (data && data.message) || 'No se pudo renovar los intentos.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                    });
                }
            });
        });
    });
});
</script>
