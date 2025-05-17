<!DOCTYPE html>
<html lang="es" data-bs-theme="auto">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <script>
    (() => {
        const storedTheme = localStorage.getItem('theme');
        const getPreferredTheme = () => {
            if (storedTheme) return storedTheme;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };
        const theme = getPreferredTheme();
        if (theme === 'auto') {
            document.documentElement.removeAttribute('data-bs-theme');
        } else {
            document.documentElement.setAttribute('data-bs-theme', theme);
        }
    })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/gridstack.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Minaguas</title>
    <script>
        (function () {
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (window.innerWidth > 768 && isCollapsed) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar d-flex flex-column p-3" id="sidebar">
        <div class="d-flex align-items-center mb-3 text-decoration-none text-white">
            <img src="{{ asset('img/splash.webp') }}" alt="logo" width="40px">
            <span class="fs-5 fw-bold ms-2 px-3">MinAguas</span>
        </div>
        <hr class="text-secondary">
        <ul class="nav nav-pills flex-column gap-2">
            <!-- Panel -->
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link {{ Route::is('home') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>
                    <span>Panel</span>
                </a>
            </li>

            @php
                $consultarActivo = Route::is('usuarios.index', 'personas.index', 'incidencias.index', 'peticiones.index', 'mis.movimientos');
            @endphp

            <!-- Consultar -->
            <li class="nav-item">
            <a href="#layouts" class="nav-link {{ $consultarActivo ? '' : 'collapsed' }}" data-bs-toggle="collapse" aria-expanded="{{ $consultarActivo ? 'true' : 'false' }}">
                    <i class="bi bi-search me-2"></i>
                    <span>Consultar</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse {{ $consultarActivo ? 'show' : '' }}" id="layouts">
                    <ul class="navbar-nav ps-3 mt-2">
                        @can('ver empleados')
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="nav-link px-3 {{ Route::is('usuarios.index') ? 'active' : '' }}">
                                <i class="bi bi-people me-2"></i>
                                Empleados
                            </a>
                        </li>
                        @endcan
                        <li>
                            <a href="{{ route('personas.index') }}" class="nav-link px-3 {{ Route::is('personas.index') ? 'active' : '' }}">
                                <i class="bi bi-person-circle me-2"></i>
                                Personas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('categorias-personas.index') }}" class="nav-link px-3 {{ Route::is('categorias-personas.index') ? 'active' : '' }}">
                                <i class="bi bi-person-lines-fill me-2"></i>
                                categorias Personas
                            </a>
                        <li>
                            <a href="{{ route('incidencias.index') }}" class="nav-link px-3 {{ Route::is('incidencias.index') ? 'active' : '' }}">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Incidencias
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('personal-reparacion.index') }}" class="nav-link px-3 {{ Route::is('categorias-incidencias.index') ? 'active' : '' }}">
                                <i class="bi bi-exclamation-diamond me-2"></i>
                                personal Reparación
                            </a>
                        <li>
                            <a href="{{ route('peticiones.index') }}" class="nav-link px-3 {{ Route::is('peticiones.index') ? 'active' : '' }}">
                                <i class="bi bi-envelope me-2"></i>
                                Peticiones
                            </a>
                        </li>
                        @auth
                        <li>
                            <a href="{{ route('mis.movimientos') }}" class="nav-link px-3 {{ Route::is('mis.movimientos') ? 'active' : '' }}">
                                <i class="bi bi-clock-history me-2"></i>
                                Mis Movimientos
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>
            </li>

            <!-- Estadísticas -->
            @can('ver grafica incidencia')
            <li class="nav-item">
                <a href="{{ route('graficos.incidencias') }}" class="nav-link {{ Route::is('graficos.incidencias') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Estadísticas
                </a>
            </li>
            @endcan
        </ul>
        <hr class="text-secondary">
    </aside>

    <!-- Main content -->
    <main class="main-content flex-fill" id="mainContent">
        <div class="topbar d-flex align-items-center justify-content-between px-3 py-2 shadow-sm">
            <button class="btn btn-light-outline" id="menuToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center">
                <!-- tema -->
                <div class="dropdown me-3">
                    <button class="btn btn-light-outline dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Cambiar tema">
                        <i class="bi bi-circle-half" id="bd-theme-icon" aria-hidden="true"></i>
                        <span class="visually-hidden" id="bd-theme-text">Tema</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                                <i class="bi bi-brightness-high me-3" aria-hidden="true"></i>
                                Claro
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                                <i class="bi bi-moon-stars me-3" aria-hidden="true"></i>
                                Oscuro
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                                <i class="bi bi-circle-half me-3" aria-hidden="true"></i>
                                Auto
                            </button>
                        </li>
                    </ul>
                </div>
                <!-- Notificaciones -->
                <div class="dropdown me-3">
                    <button class="btn btn-light-outline position-relative" id="dropdownNotifications" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        @auth
                        @if($unreadCount = auth()->user()->notificaciones()->where('leido', false)->count())
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                        </span>
                        @endif
                        @endauth
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-notifications p-0">
                        <li class="dropdown-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                            <strong>Notificaciones</strong>
                            @auth
                            @if($unreadCount > 0)
                            <button class="btn btn-sm btn-link p-0" id="markAllAsRead">Marcar todas</button>
                            @endif
                            @endauth
                        </li>
                        @auth
                        @forelse(auth()->user()->notificaciones()->latest()->take(5)->get() as $notificacion)
                        <li>
                            <a class="dropdown-item notification-item {{ !$notificacion->leido ? 'unread' : '' }} py-2 px-3"
                               data-notification-id="{{ $notificacion->id_notificacion }}">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong>{{ $notificacion->titulo }}</strong>
                                    <small class="text-muted">{{ $notificacion->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notificacion->mensaje }}</p>
                                @if($notificacion->tipo_notificacion)
                                <span class="badge
                                    {{ $notificacion->tipo_notificacion == 'nueva_incidencia' ? 'bg-info' : '' }}
                                    {{ $notificacion->tipo_notificacion == 'nueva_persona' ? 'bg-success' : '' }}">
                                    {{ $notificacion->tipo_notificacion == 'nueva_incidencia' ? 'Incidencia' : '' }}
                                  
                                </span>
                                @endif
                            </a>
                        </li>
                        @empty
                        <li class="dropdown-item text-muted py-2 px-3">No hay notificaciones</li>
                        @endforelse
                        @endauth
                        <li class="dropdown-divider"></li>
                        <li class="text-center py-2">
                            <a href="{{ route('notificaciones.index') }}" class="text-decoration-none">Ver todas</a>
                        </li>
                    </ul>
                </div>

                <!-- Perfil usuario -->
                <div class="dropdown">
                    <button class="btn btn-light-outline dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('usuarios.configuracion') }}">Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="container py-4">
            @yield('content')
        </div>
    </main>

<!-- Scripts -->

<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/popper.js') }}"></script>
<script src="{{ asset('js/gridstack-all.min.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>