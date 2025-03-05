<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Persona y Reportes</title>
    <!-- Enlace al archivo CSS de Bootstrap -->
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
                <a href="{{ route('lideres.index') }}" class="nav-link">
                    <i class="bi bi-person-badge"></i>
                    <span>Líderes Comunitarios</span>
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
        <div class="container mt-5">
            @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger mb-3" id="error-alert">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger mb-3" id="validation-errors">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <h2 class="text-center">Datos de la Persona</h2>
            <div class="mt-3">
                <!-- Botón Volver utilizando ruta de Laravel -->
                <a href="{{ route('personas.index') }}" class="btn btn-primary fw-bold">Volver</a>
            </div>
            
            <!-- Card para mostrar la información personal -->
            <div class="card mt-4">
                <div class="card-header">
                Información Personal
                </div>
                <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $persona->nombre }}</td>
                    </tr>
                    <tr>
                        <th>Apellido:</th>
                        <td>{{ $persona->apellido }}</td>
                    </tr>
                    <tr>
                        <th>Cédula:</th>
                        <td>{{ $persona->cedula }}</td>
                    </tr>
                    <tr>
                        <th>Correo Electrónico:</th>
                        <td>{{ $persona->correo }}</td>
                    </tr>
                    <tr>
                        <th>Teléfono:</th>
                        <td>{{ $persona->telefono }}</td>
                    </tr>
                    <tr>
                        <th>Responsable:</th>
                        <td>{{ $persona->user->nombre }} {{ $persona->user->apellido }}</td>
                    </tr>
                    <tr>
                        <th>Creado en:</th>
                        <td>{{ $persona->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Card para mostrar las direcciones -->
            <div class="card mt-4">
                <div class="card-header">
                Direcciones
                </div>
                <div class="card-body">
                @foreach($persona->direccion as $direccion)
                <div class="card mb-4">
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>Estado:</th>
                                    <td>{{ $direccion->estado ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Municipio:</th>
                                    <td>{{ $direccion->municipio ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Parroquia:</th>
                                    <td>{{ $direccion->parroquia->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Urbanización:</th>
                                    <td>{{ $direccion->urbanizacion->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Sector:</th>
                                    <td>{{ $direccion->sector->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Comunidad:</th>
                                    <td>{{ $direccion->comunidad->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Calle:</th>
                                    <td>{{ $direccion->calle ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Manzana:</th>
                                    <td>{{ $direccion->manzana ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Número de Casa:</th>
                                    <td>{{ $direccion->numero_de_casa ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>¿Es líder Comunitario?</th>
                                    <td>
                                        <span class="
                                        @if($direccion->esLider)
                                            text-success  <!-- Clase para color verde -->
                                        @else
                                            text-danger  <!-- Clase para color rojo -->
                                        @endif
                                        ">
                                        {{ $direccion->esLider ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha de Registro:</th>
                                    <td>{{ $direccion->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
                </div>
            </div>

            <hr>

            <h3>Reportes de Incidencias</h3>
            @if($persona->incidencias->isEmpty())
                <p>No hay incidencias registradas para esta persona.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipo de Incidencia</th>
                            <th>Descripción</th>
                            <th>Nivel de Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($persona->incidencias as $incidencia)
                            <tr>
                                <td>{{ $incidencia->tipo_incidencia }}</td>
                                <td>{{ $incidencia->descripcion }}</td>
                                <td>{{ $incidencia->nivel_prioridad }}</td>
                                <td>{{ $incidencia->estado }}</td>
                                <td>{{ $incidencia->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('incidencias.edit', ['slug' => $incidencia->slug, 'persona_slug' => $persona->slug]) }}">Modificar incidencia</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Enlace al JS de Bootstrap -->
        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/script.js') }}"></script>
        <script>
            setTimeout(function() {
                document.getElementById('error-alert')?.style.display = 'none';
                document.getElementById('validation-errors')?.style.display = 'none';
            }, 2000);
        </script>
    </div>
</body>
</html>
