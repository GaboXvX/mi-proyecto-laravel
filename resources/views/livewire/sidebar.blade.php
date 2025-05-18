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