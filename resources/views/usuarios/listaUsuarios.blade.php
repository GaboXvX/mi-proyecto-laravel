<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    
    <!-- Enlazar los archivos CSS mediante asset() -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>

</head>

<body>
  <!-- Sidebar -->
  <nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <a href="{{ route('home') }}" class="d-flex align-items-center mb-3 text-decoration-none text-white">
        <img src="{{ asset('img/splash.webp') }}" alt="logo" width="40px">
        <span class="fs-5 fw-bold ms-2 px-3">MinAguas</span>
    </a>
    <hr class="text-secondary">
    <ul class="nav nav-pills flex-column">
        <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                <span>Panel</span>
            </a>
        </li>
       
        <li class="nav-item">
            <a href="#layouts" class="nav-link" data-bs-toggle="collapse">
                <i class="bi bi-search"></i>
                <span>Consultar</span>
                <span class="right-icon px-2"><i class="bi bi-chevron-down"></i></span>
            </a>
            <div class="collapse" id="layouts">
                <ul class="navbar-nav ps-3">
                    @role('admin')
                    <li>
                        <a href="{{ route('usuarios.index') }}" class="nav-link px-3">
                            <i class="bi bi-people"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>
                    @endrole
                    <li>
                        <a href="{{ route('personas.index') }}" class="nav-link px-3">
                            <i class="bi bi-person-circle"></i>
                            <span>Personas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('incidencias.index') }}" class="nav-link px-3">
                            <i class="bi bi-exclamation-triangle"></i>
                            <span>Incidencias</span>
                        </a>
                    </li>
                    @role('admin')
                    <li>
                        <a href="{{ route('peticiones.index') }}" class="nav-link px-3">
                            <i class="bi bi-envelope"></i>
                            <span>Peticiones</span>
                        </a>
                    </li>
                   
                    @endrole
                </ul>
            </div>
        </li>
        @role('admin')
        <li class="nav-item">
            <a href="{{ route('estadisticas') }}" class="nav-link">
                <i class="bi bi-bar-chart-line"></i>
                <span>Estadísticas</span>
            </a>
        </li>
        @endrole
    </ul>
    <hr class="text-secondary">
</nav>

<!-- Main Content -->
<div class="main-content">
    <!-- Topbar -->
    <div class="topbar d-flex align-items-center justify-content-between">
        <button class="btn btn-light burger-btn" id="menuToggle">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <button class="btn btn-light me-2">
                <i class="bi bi-bell"></i>
            </button>
            <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('usuarios.configuracion') }}">Configuración</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="dropdown-item">Cerrar sesión</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenido -->
    <div class="table-container">
        <h2>Lista de Usuarios</h2>

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
                        <th>Rol</th> <!-- Nueva columna para el rol -->
                        <th>Estado</th>
                        <th>Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->nombre }}</td>
                            <td>{{ $usuario->apellido }}</td>
                            <td>{{ $usuario->cedula }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->role ? $usuario->role->rol : 'Sin rol' }}</td> <!-- Mostrar el rol -->
                            <td>
                                @if ($usuario->id_estado_usuario == 1)
                                    Activo
                                @elseif ($usuario->id_estado_usuario == 2)
                                    Desactivado
                                @else
                                    Desconocido
                                @endif
                            </td>
                            <td>{{ $usuario->created_at }}</td>
                            <td>
                                <form action="{{ route('usuarios.restaurar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Restaurar</button>
                                </form>

                                <!-- Mostrar el botón de desactivar solo si el usuario listado no es admin y el usuario autenticado no es admin -->
                                @if ($usuario->id_rol==2 && $usuarioAutenticado->id_rol == 1)
                                    @if ($usuario->id_estado_usuario == 1)
                                        <form action="{{ route('usuarios.desactivar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm">Deshabilitar</button>
                                        </form>
                                    @else
                                        <form action="{{ route('usuarios.activar', $usuario->id_usuario) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Activar</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Incluir los scripts de Bootstrap -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>
</body>


</html>