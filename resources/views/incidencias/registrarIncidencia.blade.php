<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Captura de Datos</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>
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

    <div class="main-content">
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

        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Formulario de Captura de Datos</h2>
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

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('incidencias.store') }}" method="POST">
                @csrf

                <input type="text" name="id_persona" value="{{ $persona->id_persona }}" class="form-control mb-3" readonly hidden>

                <div class="mb-3">
                    <label for="tipo_incidencia" class="form-label">Tipo de incidencia:</label>
                    <select id="tipo_incidencia" name="tipo_incidencia" class="form-select" required>
                        <option value="" disabled selected>--Seleccione--</option>
                        <option value="agua potable" {{ old('tipo_incidencia') == 'agua potable' ? 'selected' : '' }}>Agua Potable</option>
                        <option value="agua servida" {{ old('tipo_incidencia') == 'agua servida' ? 'selected' : '' }}>Agua Servida</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción:</label>
                    <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="nivel_prioridad" class="form-label">Nivel Incidencia:</label>
                    <select name="nivel_prioridad" id="nivel_prioridad" class="form-select" required>
                        <option value="" disabled selected>--Seleccione--</option>
                        <option value="1" {{ old('nivel_prioridad') == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ old('nivel_prioridad') == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ old('nivel_prioridad') == '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ old('nivel_prioridad') == '4' ? 'selected' : '' }}>4</option>
                        <option value="5" {{ old('nivel_prioridad') == '5' ? 'selected' : '' }}>5</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="estado" class="form-label">Estado:</label>
                    <input type="text" id="estado" name="estado" value="Por atender" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección:</label>
                    <select id="direccion" name="direccion" class="form-select" required>
                        <option value="" disabled selected>--Seleccione--</option>
                        @foreach ($persona->direccion as $direccion)
                            <option value="{{ $direccion->id_direccion }}">
                                Parroquia: {{ $direccion->parroquia->nombre }} - Urbanización: {{ $direccion->urbanizacion->nombre }} - Sector: {{ $direccion->sector->nombre }} - Comunidad: {{ $direccion->comunidad->nombre }} - Calle: {{ $direccion->calle }} Manzana: {{ $direccion->manzana }} Número de Casa: {{ $direccion->numero_de_casa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" name="btn-enviar" class="btn btn-primary">Enviar</button>
            </form>

            <div class="mt-3">
                <a href="{{ route('personas.index') }}" class="btn btn-link">Ir a la lista de personas</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.2/dist/chart.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
