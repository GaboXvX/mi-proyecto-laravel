<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
    <title>Minaguas - admin</title>
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
                <h2>Lista de Personas</h2>
                <div>
                    <a href="{{ route('personas.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i>
                    </a>
                </div>
            </div>

            <div class="mb-4">
                <input type="search" id="buscar" placeholder="Buscar por cédula" class="form-control d-inline-block" style="width: auto;">
            </div>

            <div id="personas-lista">
                @if (!empty($personas) && count($personas) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Cédula</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="personas-tbody">
                                @foreach ($personas as $persona)
                                    <tr>
                                        <td>{{ $persona->nombre }}</td>
                                        <td>{{ $persona->apellido }}</td>
                                        <td>{{ $persona->cedula }}</td>
                                        <td>{{ $persona->correo }}</td>
                                        <td>{{ $persona->telefono }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('personas.show', $persona->slug) }}" class="btn btn-info btn-sm">Ver</a>
                                                <a href="{{ route('incidencias.crear', $persona->slug) }}" class="btn btn-success btn-sm">Añadir Incidencia</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="alert alert-warning">No se encontró ninguna persona con esa cédula.</p>
                @endif
            </div>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        class BuscadorPersonas {
    constructor(inputId, tbodyId, url) {
        this.input = document.getElementById(inputId);
        this.tbody = document.getElementById(tbodyId);
        this.url = url;

        // Agregar event listener al campo de búsqueda
        this.input.addEventListener('input', () => this.buscarPersonas());
    }

    async buscarPersonas() {
        const query = this.input.value.trim();
        if (!query) {
            this.tbody.innerHTML = ''; // Limpiar si no hay consulta
            return;
        }

        try {
            const response = await fetch(this.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ query })
            });

            const personas = await response.json();
            this.mostrarResultados(personas);
        } catch (error) {
            console.error('Error al buscar personas:', error);
        }
    }

    mostrarResultados(personas) {
        this.tbody.innerHTML = personas.map(persona => `
            <tr>
                <td>${persona.nombre}</td>
                <td>${persona.apellido}</td>
                <td>${persona.cedula}</td>
                <td>${persona.correo}</td>
                <td>${persona.telefono}</td>
                <td>
                    <div class="btn-group">
                        <a href="/persona/${persona.slug}" class="btn btn-info btn-sm">Ver</a>
                        <a href="/persona/${persona.slug}/incidencias/create" class="btn btn-success btn-sm">Añadir Incidencia</a>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

    // Inicializar la clase con los elementos del DOM y la URL del backend
    document.addEventListener('DOMContentLoaded', () => {
        new BuscadorPersonas('buscar', 'personas-tbody', '{{ route('personas.buscar') }}');
    });
    </script>
</body>

</html>
