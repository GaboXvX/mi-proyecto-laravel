<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Líderes</title>
    <!-- Cargar Bootstrap y otros recursos con asset -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body class="bg-light">


    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column p-3" id="sidebar">
        <a href="{{ route('home') }}" class="d-flex align-items-center mb-3 text-decoration-none text-white">
            <!-- Imagen -->
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
                <a href="{{ route('lideres.index') }}" class="nav-link">
                    <i class="bi bi-person-badge"></i>
                    <span>Líderes Comunitarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#layouts" class="nav-link" data-bs-toggle="collapse" aria-expanded="false">
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
                            <li>
                                <a href="{{ route('movimientos.index') }}" class="nav-link px-3">
                                    <i class="bi bi-arrow-left-right"></i>
                                    <span>Movimientos</span>
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
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="container my-5">
            <h1 class="mb-4 text-center">Lista de Líderes</h1>

            <form action="{{ route('lideres.buscar') }}" method="POST">
                @csrf
                <div class="d-flex filters-container gap-2">
                    <input type="search" name="buscar" id="buscar" placeholder="Buscar por cédula"
                        class="form-control d-inline-block" style="width: auto;">
                    <button type="submit" class="btn btn-primary ms-2"><i class="bi bi-search"></i> Buscar</button>
                </div>
            </form>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif



            @if ($lideres->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Cédula</th>
                                <th>Correo Electrónico</th>
                                <th>Teléfono</th>
                                <th>estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lideres as $lider)
                                <tr>
                                    <td>{{ $lider->nombre }}</td>
                                    <td>{{ $lider->apellido }}</td>
                                    <td>{{ $lider->cedula }}</td>
                                    <td>{{ $lider->correo }}</td>
                                    <td>{{ $lider->telefono }}</td>
                                    <td>
                                        <span class="
                                            @if ($lider->lider_comunitario->first()->estado == 1) 
                                                text-success  <!-- Verde para activo -->
                                            @elseif($lider->lider_comunitario->first()->estado == 0) 
                                                text-danger  <!-- Rojo para inactivo -->
                                            @endif
                                        ">
                                            {{ $lider->lider_comunitario->first()->estado ? 'activo' : 'inactivo' }}
                                        </span>
                                   
                                    
                                        <div class="btn-group">
                                            <a href="{{ route('personas.show', $lider->slug) }}"
                                                class="btn btn-info btn-sm">Ver</a>
                                            <a href="{{ route('personas.edit', $lider->slug) }}"
                                                class="btn btn-warning btn-sm">Editar</a>
                                            <a href="{{ route('incidencias.crear', $lider->slug) }}"
                                                class="btn btn-success btn-sm">Añadir Incidencia</a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="alert alert-warning">No se encontró ningún líder con esa cédula.</p>
            @endif
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.2/dist/chart.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
