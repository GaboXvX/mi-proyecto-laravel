<table class="table table-striped align-middle datatable">
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
            <th>Ejerce</th>
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
                <td>{{ $usuario->empleadoAutorizado->nacionalidad ?? '-' }}-{{ $usuario->empleadoAutorizado->cedula ?? '-' }}</td>
                <td>{{ $usuario->empleadoAutorizado->cargo->nombre_cargo ?? 'No definido' }}</td>
                <td>{{ $usuario->nombre_usuario ?? '-' }}</td>
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
                <td>
                    @if($usuario->empleadoAutorizado)
                        @if($usuario->empleadoAutorizado->es_activo)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </td>
<td>{{ $usuario->created_at ? $usuario->created_at->setTimezone('America/Caracas')->format('d/m/Y h:i A') : '-' }}</td>                <td>
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
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                            </svg>
                                                Inspeccionar
                                            </a>
                                        </li>
                                    @endcan
                                        <li>
                                            <a class="dropdown-item" href="{{ route('empleados.edit', $usuario->empleadoAutorizado->id_empleado_autorizado) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg>
                                                Editar empleado
                                            </a>
                                        </li>
                                    @can('desactivar empleados')
                                        @if ($usuario->id_estado_usuario == 1)
                                          @if($usuario->empleadoAutorizado->es_activo)  
                                        <li>
                                                <form action="{{ route('usuarios.desactivar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">  
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-dash" viewBox="0 0 16 16">
  <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M11 12h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1 0-1m0-7a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  <path d="M2 13c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4"/>
</svg>
                                                        Deshabilitar
                                                    </button>
                                                </form>
                                            </li>
                                            @endcan
                                        @endif
                                    @endcan
                                    @can('habilitar empleados')
                                        @if ($usuario->id_estado_usuario == 2)
                                            @if($usuario->empleadoAutorizado->es_activo)
                                        <li>
                                                <form action="{{ route('usuarios.activar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check-fill" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
  <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
</svg>
                                                        Activar
                                                    </button>
                                                </form>
                                            </li>
                                            @endcan
                                        @endif
                                    @endcan
                                    <li>
                                        <form action="{{ route('usuarios.restaurar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
  <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
</svg>
                                                Restaurar
                                            </button>
                                        </form>
                                    </li>
                                    @if (auth()->user()->hasRole('admin') && $usuario->hasRole('registrador'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('usuarios.asignarPermisos', $usuario->id_usuario) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
  <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
  <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415"/>
</svg>
                                                Permisos
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <button type="button" class="dropdown-item btn-renovar-intentos" data-id="{{ $usuario->id_usuario }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
  <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
  <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
</svg>
                                            Renovar intentos
                                        </button>
                                    </li>
                                     @if($usuario->empleadoAutorizado->es_activo)
                                        <li>
                                            <button type="button" class="dropdown-item btn-retirar" data-id="{{ $usuario->empleadoAutorizado->id_empleado_autorizado }}">
                                                <i class="bi bi-person-x me-2"></i>Retirar
                                            </button>
                                        </li>
                                    @else
                                        <li>
                                            <button type="button" class="dropdown-item btn-incorporar" data-id="{{ $usuario->empleadoAutorizado->id_empleado_autorizado }}">
                                                <i class="bi bi-person-check me-2"></i>Incorporar
                                            </button>
                                        </li>
                                        
                                    @endif
                                     <a class="dropdown-item" href="{{ route('empleados.historial', $usuario->empleadoAutorizado->id_empleado_autorizado) }}">
                <i class="fas fa-history mr-2"></i>Ver historial
            </a>
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
                <td>{{ $empleado->nacionalidad }}-{{ $empleado->cedula }}</td>
                <td>{{ $empleado->cargo->nombre_cargo ?? 'No definido' }}</td>
                <td><span class="text-muted">No definido</span></td>
                <td><span class="text-muted">No asignado</span></td>
                <td><span class="text-warning">Sin registrarse</span></td>
                <td><span class="text-muted">No asignado</span></td>
                <td>
                    @if($empleado->es_activo)
                        <span class="badge bg-success">Sí</span>
                    @else
                        <span class="badge bg-danger">No</span>
                    @endif
                </td>
<td>{{ $empleado->created_at?->setTimezone('America/Caracas')->format('d/m/Y h:i A') ?? '-' }}</td>                <td>
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
                            @if($empleado->es_activo)
                                <li>
                                    <button type="button" class="dropdown-item btn-retirar" data-id="{{ $empleado->id_empleado_autorizado }}">
                                        <i class="bi bi-person-x me-2"></i>Retirar
                                    </button>
                                </li>
                            @else
                                <li>
                                    <button type="button" class="dropdown-item btn-incorporar" data-id="{{ $empleado->id_empleado_autorizado }}">
                                        <i class="bi bi-person-check me-2"></i>Incorporar
                                    </button>
                                </li>
                                
                            @endif
                             <a class="dropdown-item" href="{{ route('empleados.historial', $empleado->id_empleado_autorizado) }}">
                <i class="fas fa-history mr-2"></i>Ver historial
            </a>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal para retirar empleado -->
<div class="modal fade" id="retirarModal" tabindex="-1" aria-labelledby="retirarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="retirarModalLabel">Retirar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="retirarForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="empleado_id" id="empleado_id_retirar">
                    <div class="mb-3">
                        <label for="observacion_retirar" class="form-label">Motivo del retiro</label>
                        <textarea class="form-control" id="observacion_retirar" name="observacion" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Retiro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para incorporar empleado -->
<div class="modal fade" id="incorporarModal" tabindex="-1" aria-labelledby="incorporarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incorporarModalLabel">Incorporar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="incorporarForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="empleado_id" id="empleado_id_incorporar">
                    <div class="mb-3">
                        <label for="observacion_incorporar" class="form-label">Motivo de la incorporación</label>
                        <textarea class="form-control" id="observacion_incorporar" name="observacion" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Incorporación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar modal y formulario para retirar empleado
    const retirarModal = new bootstrap.Modal(document.getElementById('retirarModal'));
    document.querySelectorAll('.btn-retirar').forEach(btn => {
        btn.addEventListener('click', function() {
            const empleadoId = this.getAttribute('data-id');
            document.getElementById('empleado_id_retirar').value = empleadoId;
            document.getElementById('retirarForm').action = `/empleados/${empleadoId}/retirar`;
            retirarModal.show();
        });
    });

    // Configurar modal y formulario para incorporar empleado
    const incorporarModal = new bootstrap.Modal(document.getElementById('incorporarModal'));
    document.querySelectorAll('.btn-incorporar').forEach(btn => {
        btn.addEventListener('click', function() {
            const empleadoId = this.getAttribute('data-id');
            document.getElementById('empleado_id_incorporar').value = empleadoId;
            document.getElementById('incorporarForm').action = `/empleados/${empleadoId}/incorporar`;
            incorporarModal.show();
        });
    });

    // Manejar envío del formulario de retiro
    document.getElementById('retirarForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                observacion: document.getElementById('observacion_retirar').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
            retirarModal.hide();
        })
        .catch(error => {
            Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
            retirarModal.hide();
        });
    });

    // Manejar envío del formulario de incorporación
    document.getElementById('incorporarForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                observacion: document.getElementById('observacion_incorporar').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
            incorporarModal.hide();
        })
        .catch(error => {
            Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
            incorporarModal.hide();
        });
    });
});
</script>