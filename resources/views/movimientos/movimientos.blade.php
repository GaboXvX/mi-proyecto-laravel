<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Historial de Movimientos</title>

    <!-- Enlace a Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
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
                        <li><a href="{{ route('usuarios.index') }}" class="nav-link px-3"><i class="bi bi-people"></i><span>Usuarios</span></a></li>
                        <li><a href="{{ route('personas.index') }}" class="nav-link px-3"><i class="bi bi-person-circle"></i><span>Personas</span></a></li>
                        <li><a href="{{ route('incidencias.index') }}" class="nav-link px-3"><i class="bi bi-exclamation-triangle"></i><span>Incidencias</span></a></li>
                        <li><a href="{{ route('peticiones.index') }}" class="nav-link px-3"><i class="bi bi-envelope"></i><span>Peticiones</span></a></li>
                        <li><a href="{{ route('movimientos.index') }}" class="nav-link px-3"><i class="bi bi-arrow-left-right"></i><span>Movimientos</span></a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="{{ route('estadisticas') }}" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span>Estadísticas</span>
                </a>
            </li>
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
        <div class="container-fluid mt-4">
            <h2>Historial de Movimientos</h2>

            @if ($movimientos->isEmpty())
                <div class="alert alert-warning">
                    No hay movimientos registrados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Movimiento n°</th>
                                <th>Acción</th>
                                <th>Valores Nuevos</th>
                                <th>Valores Antiguos</th>
                                <th>Usuario</th>
                                <th>Persona</th>
                                <th>Líder</th>
                                <th>Incidencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movimientos as $movimiento)
                                @php
                                    $valorAnterior = json_decode($movimiento->valor_anterior, true) ?? [];
                                    $valorNuevo = json_decode($movimiento->valor_nuevo, true) ?? [];
                                @endphp
                                <tr>
                                    <td>{{ $movimiento->id_movimiento }}</td>
                                    <td>{{ $movimiento->accion }}</td>
                                    <td>
                                        @foreach ($valorNuevo as $campo => $valor)
                                            @if ($valor) {{ ucfirst($campo) }}: {{ htmlspecialchars($valor) }}<br>@endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($valorAnterior as $campo => $valor)
                                            @if ($valor) {{ ucfirst($campo) }}: {{ htmlspecialchars($valor) }}<br>@endif
                                        @endforeach
                                    </td>
                                    <td>{{ $movimiento->usuario->nombre ?? 'N/A' }}</td>
                                    <td>{{ $movimiento->persona->nombre ?? 'N/A' }}</td>
                                    <td>{{ $movimiento->lider->nombre ?? 'N/A' }}</td>
                                    <td>{{ $movimiento->incidencia->id_incidencia ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination">
                        {{ $movimientos->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
