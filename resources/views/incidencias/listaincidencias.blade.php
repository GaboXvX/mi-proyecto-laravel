<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Incidencias</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
     <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
     <style>

        .status-pending {
            color: orange;
        }

        .status-resolved {
            color: green;
        }

        .status-closed {
            color: red;
        }

        .alert {
            border-radius: 6px;
            font-weight: bold;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }

    </style>
   
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

    <!-- Contenido -->
    <div class="table-container">
        <div class="d-flex justify-content-between align-item-center mb-3">
            <h2>Lista de Incidencias</h2>
            <div class="gen-pdf">
                @role('admin')
                <a href="{{ route('incidencias.gestionar') }}" class="btn btn-success me-2">Cambiar Estado</a>
                <button type="submit" class="btn btn-primary">Generar PDF</button>
                @endrole
            </div>
        </div>
       
        <!-- Filters -->
        <div class="d-flex filters-container gap-2">
            <form action="{{ route('incidencias.buscar') }}" method="post" class="input-group input-group-sm">
                <button class="input-group-text btn btn-primary" id="basic-addon1">
                    <i class="bi bi-search"></i>
                </button>
                <input type="text" class="form-control form-control-sm" placeholder="Ingrese un código">
                @csrf
            </form>
            <form action="{{ route('incidencias.download') }}" method="POST">
            @csrf
            <label for="fecha_inicio" class="form-label">Selecciona el período:</label>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex">
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2 mb-3" />
                        <span class="m-2">hasta</span>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control ml-2 mb-3" />
                    </div>
                    <select class="form-select form-select-sm w-50 m-2" aria-label="Select status" name="estado" id="estado">
                        <option selected>Estado de la incidencia:</option>
                        <option value="Todos">Todos</option>
                        <option value="Atendido">Atendido</option>
                        <option value="Por atender">Por atender</option>
                    </select>
                </div>
            </form>
        </div>

        <div id="resultados" class="mt-3"></div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Código de incidencia</th>
                        <th>Tipo de Incidencia</th>
                        <th>Descripción</th>
                        <th>Nivel de Prioridad</th>
                        <th>Estado</th>
                        <th>Creación</th>
                        <th>Registrado por</th>
                        <th>Líder</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="incidencias-tbody">
                    @foreach ($incidencias as $incidencia)
                        @if($incidencia && $incidencia->cod_incidencia)
                            <tr>
                                <td>{{ $incidencia->cod_incidencia }}</td>
                                <td>{{ $incidencia->tipo_incidencia }}</td>
                                <td>{{ $incidencia->descripcion }}</td>
                                <td>{{ $incidencia->nivel_prioridad }}</td>
                                <td class="incidencia-status 
                                            @if($incidencia->estado == 'Por atender') status-pending 
                                            @elseif($incidencia->estado == 'Atendido') status-resolved 
                                            @endif">
                                    {{ $incidencia->estado }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($incidencia->created_at)->format('d-m-Y H:i:s') }}</td>
                                <td>
                                    @if($incidencia->persona)
                                        {{ $incidencia->persona->nombre }} {{ $incidencia->persona->apellido }}
                                    @else
                                        No registrado
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $lider = \App\Models\Lider_Comunitario::find($incidencia->id_lider);
                                        $personaLider = $lider ? \App\Models\Persona::find($lider->id_persona) : null;
                                    @endphp
                                    @if($personaLider)
                                       {{ $personaLider->nombre }} {{ $personaLider->apellido }}
                                    @else
                                        No asignado
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('incidencias.descargar', $incidencia->slug) }}" class="btn btn-primary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="9" class="text-center">No se encontró incidencia para este código</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        class FiltroIncidencias {
    constructor(fechaInicioId, fechaFinId, estadoId, resultadosId, tbodyId, url) {
        this.fechaInicio = document.getElementById(fechaInicioId);
        this.fechaFin = document.getElementById(fechaFinId);
        this.estado = document.getElementById(estadoId);
        this.resultados = document.getElementById(resultadosId);
        this.tbody = document.getElementById(tbodyId);
        this.url = url;

        // Agregar event listeners a los filtros
        this.fechaInicio.addEventListener('change', () => this.filtrarIncidencias());
        this.fechaFin.addEventListener('change', () => this.filtrarIncidencias());
        this.estado.addEventListener('change', () => this.filtrarIncidencias());
    }

    async filtrarIncidencias() {
        const fechaInicio = this.fechaInicio.value;
        const fechaFin = this.fechaFin.value;
        const estado = this.estado.value;

        if (!fechaInicio || !fechaFin) {
            return;
        }

        try {
            const response = await fetch(this.url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ fecha_inicio: fechaInicio, fecha_fin: fechaFin, estado })
            });

            const data = await response.json();
            this.mostrarResultados(data.incidencias);
        } catch (error) {
            console.error('Error al filtrar incidencias:', error);
        }
    }

    mostrarResultados(incidencias) {
        this.tbody.innerHTML = '';

        if (incidencias && incidencias.length > 0) {
            incidencias.forEach(incidencia => {
                const tr = document.createElement('tr');
                const fecha = new Date(incidencia.created_at);
                const fechaFormateada = fecha.toLocaleString('es-ES', {
                    weekday: 'short', year: 'numeric', month: '2-digit', day: '2-digit',
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });

                tr.innerHTML = `
                    <td>${incidencia.cod_incidencia}</td>
                    <td>${incidencia.tipo_incidencia}</td>
                    <td>${incidencia.descripcion}</td>
                    <td>${incidencia.nivel_prioridad}</td>
                    <td class="incidencia-status ${this.getStatusClass(incidencia.estado)}">${incidencia.estado}</td>
                    <td>${fechaFormateada}</td>
                    <td>${incidencia.persona ? `${incidencia.persona.nombre} ${incidencia.persona.apellido}` : 'No registrado'}</td>
                    <td>${incidencia.lider ? `${incidencia.lider.nombre} ${incidencia.lider.apellido}` : 'No asignado'}</td>
                `;

                this.tbody.appendChild(tr);
            });
        } else {
            this.tbody.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron incidencias para el período y estado seleccionado.</td></tr>';
        }
    }

    getStatusClass(estado) {
        switch (estado) {
            case 'Atendido': return 'status-resolved';
            case 'Por atender': return 'status-pending';
            default: return '';
        }
    }
}

// Inicializar la clase cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new FiltroIncidencias(
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'resultados',
        'incidencias-tbody',
        '/filtrar-incidencia'
    );
});
    </script>
     <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
     <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.2/dist/chart.min.js"></script>
     <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
