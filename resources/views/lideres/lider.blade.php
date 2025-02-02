<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Lider y Reportes</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
</head>

<body>
     <!-- Sidebar -->
     <nav class="sidebar d-flex flex-column p-3" id="sidebar">
        <a href="{{route('home')}}" class="d-flex align-items-center mb-3 text-decoration-none text-white">
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
    <div class="container mt-5">

        <h2>Datos de la Lider</h2>
        <table class="table table-bordered">
            <tr>
                <th>Nombre</th>
                <td>{{ $lider->nombre }}</td>
            </tr>
            <tr>
                <th>Apellido</th>
                <td>{{ $lider->apellido }}</td>
            </tr>
            <tr>
                <th>Cédula</th>
                <td>{{ $lider->cedula }}</td>
            </tr>
            <tr>
                <th>Correo</th>
                <td>{{ $lider->correo }}</td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td>{{ $lider->telefono }}</td>
            </tr>
            <!-- Estado -->
            <tr>
            <th>Estado</th>
            @if ($lider->direccion && $lider->direccion->estado)
                

                    <td>{{ $lider->direccion->estado }}</td>
                @else
                    <td>No registrado</td>
                </tr>
            @endif
            <tr>
                <th>Municipio</th>
                <!-- Municipio -->
                @if ($lider->direccion && $lider->direccion->municipio)
                    <td>{{ $lider->direccion->municipio }}</td>
                @else
                    <td>No registrado</td>
            </tr>
            @endif
            <tr>
                <th>Comunidad</th>
                @if ($lider->direccion && $lider->direccion->comunidad)
                    <td>{{ $lider->direccion->comunidad->nombre }}</td>
                @else
                    <td>No registrado</td>
            </tr>
            @endif
            <tr>
                <th>Sector</th>
                <!-- Sector -->
                @if ($lider->direccion && $lider->direccion->sector)
                    <td>{{ $lider->direccion->sector->nombre }}</td>
                @else
                    <td>No registrado</td>
            </tr>
            @endif
            <tr>
                <th>Número de Casa</th>
                <!-- Número de Casa -->
                @if ($lider->direccion && $lider->direccion->numero_de_casa)
                    <td>{{ $lider->direccion->numero_de_casa }}</td>
                @else
                    <td>No registrado</td>
            </tr>
            @endif

            <!-- Responsable -->
            @if ($lider->user)
                <tr>
                    <th>Responsable</th>
                    <td>{{ $lider->user->nombre }} {{ $lider->user->apellido }}</td>
                </tr>
            @endif

            <!-- Creado en -->
            @if ($lider->created_at)
                <tr>
                    <th>Creado en</th>
                    <td>{{ $lider->created_at }}</td>
                </tr>
            @endif

        </table>

        <hr>


        <h3>Reportes de Incidencias</h3>
        @if ($lider->incidencias->isEmpty())
            <p>No hay incidencias registradas para esta lider.</p>
        @else
            <table class="table">
                <thead>
                    <tr>

                        <th>Tipo de Incidencia</th>
                        <th>Descripción</th>
                        <th>Nivel de Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lider->incidencias as $incidencia)
                        <tr>
                            <td>{{ $incidencia->tipo_incidencia }}</td>
                            <td>{{ $incidencia->descripcion }}</td>
                            <td>{{ $incidencia->nivel_prioridad }}</td>
                            <td>{{ $incidencia->estado }}</td>
                            <td>{{ $incidencia->created_at }}</td>
                            <td> 
                                @if($incidencia->estado =='Por atender')
                                <a
                                    href="{{ route('incidencias.edit', ['slug' => $incidencia->slug, 'persona_slug' => $lider->slug]) }}">Modificar
                                    incidencia</a>
                                   @else
                                    @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
