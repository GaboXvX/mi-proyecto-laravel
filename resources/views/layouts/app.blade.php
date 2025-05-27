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
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/icon.webp') }}" type="image/webp">
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
            <img src="{{ asset('img/icon.webp') }}" alt="logo" width="40px">
            <span class="sidebar-title">MinAguas</span>
        </div>

        <hr>
        
        <ul class="sidebar-nav">
            <!-- Panel -->
            <li class="sidebar-item">
                <a href="{{ route('home') }}" class="sidebar-link {{ Route::is('home') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-speedometer2" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.39.39 0 0 0-.029-.518z"/>
                        <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3"/>
                    </svg>
                    <span>Panel</span>
                </a>
            </li>

            @php
                $consultarActivo = Route::is('usuarios.index', 'personas.index', 'instituciones.index' , 'personal-reparacion.index' , 'niveles-incidencia.index', 'incidencias.index', 'peticiones.index', 'mis.movimientos');
            @endphp

            <!-- Consultar -->
            <li class="sidebar-item">
                <a href="#layouts" class="sidebar-link has-dropdown {{ $consultarActivo ? 'open' : '' }}" data-target="layouts">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                    <span>Consultar</span>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-dropdown {{ $consultarActivo ? 'show' : '' }}" id="layouts">
                    <ul class="dropdown-nav">
                        @can('ver empleados')
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="sidebar-link {{ Route::is('usuarios.index') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                                </svg>
                                Empleados
                            </a>
                        </li>
                        @endcan
                        <li>
                            <a href="{{ route('personas.index') }}" class="sidebar-link {{ Route::is('personas.index') ? 'active' : '' }}">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" transform="" id="injected-svg">
                                <path d="m12,11c1.71,0,3-1.29,3-3s-1.29-3-3-3-3,1.29-3,3,1.29,3,3,3Zm0-4c.6,0,1,.4,1,1s-.4,1-1,1-1-.4-1-1,.4-1,1-1Z"></path><path d="m13,12h-2c-2.76,0-5,2.24-5,5v.5c0,.83.67,1.5,1.5,1.5h9c.83,0,1.5-.67,1.5-1.5v-.5c0-2.76-2.24-5-5-5Zm-5,5c0-1.65,1.35-3,3-3h2c1.65,0,3,1.35,3,3h-8Z"></path>
                                <path d="m6.5,11c.47,0,.9-.12,1.27-.33-.48-.77-.77-1.68-.77-2.67,0-.66.13-1.28.35-1.85-.26-.09-.55-.15-.85-.15-1.44,0-2.5,1.06-2.5,2.5s1.06,2.5,2.5,2.5Z"></path><path d="m6.11,12h-.61c-1.93,0-3.5,1.57-3.5,3.5v1c0,.28.22.5.5.5h1.5c0-1.96.81-3.73,2.11-5Z"></path><path d="m17.5,11c1.44,0,2.5-1.06,2.5-2.5s-1.06-2.5-2.5-2.5c-.31,0-.59.06-.85.15.22.57.35,1.19.35,1.85,0,.99-.29,1.9-.77,2.67.37.21.79.33,1.27.33Z"></path>
                                <path d="m18.5,12h-.61c1.3,1.27,2.11,3.04,2.11,5h1.5c.28,0,.5-.22.5-.5v-1c0-1.93-1.57-3.5-3.5-3.5Z"></path>
                                </svg>
                                Personas
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('incidencias.index') }}" class="sidebar-link {{ Route::is('incidencias.index') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                </svg>
                                Incidencias
                            </a>
                        </li>
                        <li>
                            <a href="{{route('niveles-incidencia.index')}}" class=" sidebar-link {{ Route::is('niveles-incidencia.index') ? 'active' : '' }}">
                                <i class="bi bi-stack "></i>
                                Niveles de Incidencia
                            </a>
                            
                        </li>
                        <li>
                            <a href="{{ route('personal-reparacion.index') }}" class="sidebar-link {{ Route::is('personal-reparacion.index') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-diamond" viewBox="0 0 16 16">
                                <path d="M6.95.435c.58-.58 1.52-.58 2.1 0l6.515 6.516c.58.58.58 1.519 0 2.098L9.05 15.565c-.58.58-1.519.58-2.098 0L.435 9.05a1.48 1.48 0 0 1 0-2.098zm1.4.7a.495.495 0 0 0-.7 0L1.134 7.65a.495.495 0 0 0 0 .7l6.516 6.516a.495.495 0 0 0 .7 0l6.516-6.516a.495.495 0 0 0 0-.7L8.35 1.134z"/>
                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                </svg>
                                Personal Reparación
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('instituciones.index') }}" class="sidebar-link {{ Route::is('instituciones.index') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-buildings" viewBox="0 0 16 16">
                                <path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/>
                                <path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z"/>
                                </svg>
                                Instituciones
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peticiones.index') }}" class="sidebar-link {{ Route::is('peticiones.index') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                                </svg>
                                Peticiones
                            </a>
                        </li>
                        @auth
                        <li>
                            <a href="{{ route('mis.movimientos') }}" class="sidebar-link {{ Route::is('mis.movimientos') ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                                <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                                <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                                <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                                </svg>
                                Mis Movimientos
                            </a>
                        </li>
                        @endauth
                    
                </div>
            

            <!-- Estadísticas -->
            @can('ver grafica incidencia')
            <li class="sidebar-item">
                <a href="{{ route('graficos.incidencias') }}" class="sidebar-link {{ Route::is('graficos.incidencias') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart-line" viewBox="0 0 16 16">
                        <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1zm1 12h2V2h-2zm-3 0V7H7v7zm-5 0v-3H2v3z"/>
                    </svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                </svg>
            </button>
            <div class="d-flex align-items-center">
                
                <!-- tema -->
                <div class="dropdown me-3">
                    <button class="btn btn-light-outline dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" aria-label="Cambiar tema">
                    <svg id="bd-theme-icon" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="18" height="18" fill="currentColor" class="me-2" viewBox="0 0 16 16">
      <path d="M8 15A7 7 0 1 0 8 1zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16"/>
    </svg>
                        <span class="visually-hidden" id="bd-theme-text">Tema</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                            <svg class="bi bi-sun me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
          <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>
        </svg>
                                Claro
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                            <svg class="bi bi-moon-stars me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
          <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
          <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.73 1.73 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.73 1.73 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.73 1.73 0 0 0 1.097-1.097zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z"/>
        </svg>
                                Oscuro
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                            <svg class="bi bi-circle-half me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
          <path d="M8 15A7 7 0 1 0 8 1zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16"/>
        </svg>
                                Auto
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Notificaciones -->
                <div class="dropdown me-3">
                    <button class="btn btn-light-outline position-relative" id="dropdownNotifications" data-bs-toggle="dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                        </svg>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                        </svg>
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
<script src="{{ asset('js/script.js') }}"></script>
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/popper.js') }}"></script>
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js')}}"></script>

<script>
    $(document).ready(function () {
    $('.datatable').DataTable({
        paging: true, 
        ordering: false,     
        searching: false, 
        responsive: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
      },
      pageLength: 10
    });
  });
</script>

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
document.addEventListener('DOMContentLoaded', () => {
  const themeButtons = document.querySelectorAll('[data-bs-theme-value]');
  const icon = document.getElementById('bd-theme-icon');
  const text = document.getElementById('bd-theme-text');

  // Aplicar tema guardado en localStorage al cargar
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    applyTheme(savedTheme);
  }

  // Escuchar clicks en los botones de tema
  themeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const selectedTheme = btn.getAttribute('data-bs-theme-value');
      localStorage.setItem('theme', selectedTheme);
      applyTheme(selectedTheme);
    });
  });

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    updateIcon(theme);
  }

  function updateIcon(theme) {
    let svgContent = '';
    switch (theme) {
      case 'light':
        svgContent = `
          <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>`;
        text.textContent = 'Claro';
        break;
      case 'dark':
        svgContent = `
          <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
          <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.73 1.73 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.73 1.73 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.73 1.73 0 0 0 1.097-1.097zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z"/>`;
        text.textContent = 'Oscuro';
        break;
      default:
        svgContent = `<path d="M8 15A7 7 0 1 0 8 1zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16"/>`;
        text.textContent = 'Auto';
        break;
    }

    icon.innerHTML = svgContent;
  }
});
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
