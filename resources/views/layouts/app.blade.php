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
    <aside class="custom-sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('img/splash.webp') }}" alt="logo" width="40px">
            <span class="sidebar-title">MinAguas</span>
        </div>

        <hr>
        
        <ul class="sidebar-nav">
            <!-- Panel -->
            <li class="sidebar-item">
                <a href="{{ route('home') }}" class="sidebar-link {{ Route::is('home') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Panel</span>
                </a>
            </li>

            @php
                $consultarActivo = Route::is('usuarios.index', 'personas.index', 'instituciones.index' , 'personal-reparacion.index' , 'incidencias.index', 'peticiones.index', 'mis.movimientos');
            @endphp

            <!-- Consultar -->
            <li class="sidebar-item">
                <a href="#layouts" class="sidebar-link has-dropdown {{ $consultarActivo ? 'open' : '' }}" data-target="layouts">
                    <i class="bi bi-search"></i>
                    <span>Consultar</span>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-dropdown {{ $consultarActivo ? 'show' : '' }}" id="layouts">
                    <ul class="dropdown-nav">
                        @can('ver empleados')
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="sidebar-link {{ Route::is('usuarios.index') ? 'active' : '' }}">
                                <i class="bi bi-people"></i>
                                Empleados
                            </a>
                        </li>
                        @endcan
                        <li>
                            <a href="{{ route('personas.index') }}" class="sidebar-link {{ Route::is('personas.index') ? 'active' : '' }}">
                                <i class="bi bi-person-circle"></i>
                                Personas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('incidencias.index') }}" class="sidebar-link {{ Route::is('incidencias.index') ? 'active' : '' }}">
                                <i class="bi bi-exclamation-triangle"></i>
                                Incidencias
                            </a>
                        </li>
                        <li>
                            <a href="{{route('niveles-incidencia.index')}}" class=" sidebar-link {{ Route::is('niveles-incidencia.index') ? 'active' : '' }}">
<i class="bi bi-stack "></i> <!-- Pilas -->
                                Niveles de Incidencia
                            </a>
                            
                        </li>
                        <li>
                            <a href="{{ route('personal-reparacion.index') }}" class="sidebar-link {{ Route::is('personal-reparacion.index') ? 'active' : '' }}">
                                <i class="bi bi-exclamation-diamond"></i>
                                Personal Reparación
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('instituciones.index') }}" class="sidebar-link {{ Route::is('instituciones.index') ? 'active' : '' }}">
                                <i class="bi bi-building"></i>
                                Instituciones
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peticiones.index') }}" class="sidebar-link {{ Route::is('peticiones.index') ? 'active' : '' }}">
                                <i class="bi bi-envelope"></i>
                                Peticiones
                            </a>
                        </li>
                        @auth
                        <li>
                            <a href="{{ route('mis.movimientos') }}" class="sidebar-link {{ Route::is('mis.movimientos') ? 'active' : '' }}">
                                <i class="bi bi-clock-history"></i>
                                Mis Movimientos
                            </a>
                        </li>
                        @endauth
                    
                </div>
            

            <!-- Estadísticas -->
            @can('ver grafica incidencia')
            <li class="sidebar-item">
                <a href="{{ route('graficos.incidencias') }}" class="sidebar-link {{ Route::is('graficos.incidencias') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i>
                    Estadísticas
                </a>
            </li>
            @endcan
        </ul>
        <hr>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggles = document.querySelectorAll(".has-dropdown");

        toggles.forEach(toggle => {
            toggle.addEventListener("click", function (e) {
                e.preventDefault();
                const targetId = this.getAttribute("data-target");
                const dropdown = document.getElementById(targetId);

                if (dropdown) {
                    dropdown.classList.toggle("show");
                    this.classList.toggle("open");
                }
            });
        });
    });
</script>
  
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Marcar notificación como leída
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function () {
                const notificationId = this.dataset.notificationId;
                if (notificationId) {
                    fetch(`/notificaciones/marcar-leida/${notificationId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        this.classList.remove('unread');
                        updateNotificationCount();
                    });
                }
            });
        });

        // Marcar todas como leídas
        document.getElementById('markAllAsRead')?.addEventListener('click', function () {
            fetch('/notificaciones/marcar-todas-leidas', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.classList.remove('unread');
                });
                updateNotificationCount();
            });
        });

        // Contador en vivo
        function updateNotificationCount() {
            fetch('/notificaciones/contador')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('#dropdownNotifications .badge');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'block' : 'none';
                    }
                });
        }

        setInterval(updateNotificationCount, 60000);
    });
</script>
<script>
        (() => {
        'use strict';

        const storedTheme = localStorage.getItem('theme');

        const getPreferredTheme = () => {
            if (storedTheme) return storedTheme;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };

        const updateThemeIcon = (theme) => {
            const icon = document.getElementById('bd-theme-icon');
            const text = document.getElementById('bd-theme-text');
            if (!icon || !text) return;

            switch (theme) {
                case 'light':
                    icon.className = 'bi bi-brightness-high me-2';
                    text.textContent = 'Claro';
                    break;
                case 'dark':
                    icon.className = 'bi bi-moon-stars me-2';
                    text.textContent = 'Oscuro';
                    break;
                default:
                    icon.className = 'bi bi-circle-half me-2';
                    text.textContent = 'Auto';
                    break;
            }
        };

        const setTheme = (theme) => {
            if (theme === 'auto') {
                document.documentElement.removeAttribute('data-bs-theme');
            } else {
                document.documentElement.setAttribute('data-bs-theme', theme);
            }
            updateThemeIcon(theme);
        };

        const currentTheme = getPreferredTheme();
        setTheme(currentTheme);

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (!storedTheme || storedTheme === 'auto') {
                setTheme(getPreferredTheme());
            }
        });

        window.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-bs-theme-value]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const theme = btn.getAttribute('data-bs-theme-value');
                    localStorage.setItem('theme', theme);
                    setTheme(theme);
                });
            });
        });
    })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuId = 'layouts';
            const collapseElement = document.getElementById(menuId);
            const toggleElement = document.querySelector(`[href="#${menuId}"]`);
        
            if (!collapseElement || !toggleElement) return;
        
            const openedByBlade = collapseElement.classList.contains('show');
            const currentRouteIsPartOfMenu = toggleElement.classList.contains('active');
        
            // Solo usar localStorage si Laravel no lo abrió ni está en una ruta activa
            if (!openedByBlade && !currentRouteIsPartOfMenu) {
                const isOpen = localStorage.getItem('sidebar-' + menuId) === 'open';
                if (isOpen) {
                    collapseElement.classList.add('show');
                    toggleElement.setAttribute('aria-expanded', 'true');
                }
            }
        
            // Eventos Bootstrap correctos para guardar estado real
            collapseElement.addEventListener('shown.bs.collapse', () => {
                localStorage.setItem('sidebar-' + menuId, 'open');
            });
        
            collapseElement.addEventListener('hidden.bs.collapse', () => {
                localStorage.setItem('sidebar-' + menuId, 'closed');
            });
        });
    </script> 
</body>
</html>
