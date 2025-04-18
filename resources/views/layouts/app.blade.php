<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
    <title>Minaguas</title>
</head>
<body>
    <aside class="sidebar d-flex flex-column p-3" id="sidebar">
        <div class="d-flex align-items-center mb-3 text-decoration-none text-white">
            <img src="{{ asset('img/splash.webp') }}" alt="logo" width="40px">
            <span class="fs-5 fw-bold ms-2 px-3">MinAguas</span>
        </div>
        <hr class="text-secondary">
        <ul class="nav nav-pills flex-column gap-2">
            <li class="nav-item active">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="bi bi-speedometer2 me-2"></i>
                    <span>Panel</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="#layouts" class="nav-link" data-bs-toggle="collapse">
                    <i class="bi bi-search me-2"></i>
                    <span>Consultar</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="layouts">
                    <ul class="navbar-nav ps-3">
                        @can('ver empleados')
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="nav-link px-3">
                                <i class="bi bi-people me-2"></i>
                                Empleados
                            </a>
                        </li>
                        @endcan
                        <li>
                            <a href="{{ route('personas.index') }}" class="nav-link px-3">
                                <i class="bi bi-person-circle me-2"></i>
                                Personas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('incidencias.index') }}" class="nav-link px-3">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Incidencias
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peticiones.index') }}" class="nav-link px-3">
                                <i class="bi bi-envelope me-2"></i>
                                Peticiones
                            </a>
                        </li>
                        @auth
                        <li>
                            <a class="nav-link px-3" href="{{ route('mis.movimientos') }}">
                                <i class="bi bi-clock-history me-2"></i>
                                Mis Movimientos
                            </a>
                        </li>
                        @endauth
                    </ul>
                </div>
            </li>

            @can('ver grafica incidencia')
            <li class="nav-item">
                <a href="{{ route('estadisticas') }}" class="nav-link">
                    <i class="bi bi-bar-chart-line me-2"></i>
                    Estadísticas
                </a>
                @endcan
            </li>
            
        </ul>
        <hr class="text-secondary">
    </aside>

    <!-- Main content -->
    <main class="main-content flex-fill">
        <div class="topbar d-flex align-items-center justify-content-between px-3 py-2 shadow-sm">
            <button class="btn btn-light burger-btn" id="menuToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center">
                <!-- Notificaciones -->
                <div class="dropdown me-3">
                    <button class="btn btn-light position-relative" id="dropdownNotifications" data-bs-toggle="dropdown">
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
                                    {{ $notificacion->tipo_notificacion == 'nueva_persona' ? 'Persona' : '' }}
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
                            <a href="#" class="text-decoration-none">Ver todas</a>
                        </li>
                    </ul>
                </div>

                <!-- Perfil usuario -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
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
</div>

<!-- Scripts -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>
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
</body>
</html>
