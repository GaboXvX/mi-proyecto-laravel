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
     <aside class="sidebar d-flex flex-column p-3" id="sidebar">
        <a href="{{route('home')}}" class="d-flex align-items-center mb-3 text-decoration-none text-white">
            <img src="{{ asset('img/splash.webp') }}" alt="logo" width="40px">
            <span class="fs-5 fw-bold ms-2 px-3">MinAguas</span>
        </a>
        <hr class="text-secondary">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-speedometer2" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.39.39 0 0 0-.029-.518z"/>
                    <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3"/>
                </svg>
                    <span>Panel</span>
                </a>
            </li>
           
            <li class="nav-item">
                <a href="#layouts" class="nav-link" data-bs-toggle="collapse">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                </svg>
                    <span class="nav-name">Consultar</span>
                    <span class="right-icon px-2"><i class="bi bi-chevron-down"></i></span>
                </a>
                <div class="collapse" id="layouts">
                    <ul class="navbar-nav ps-3">
                        @role('admin')
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="nav-link px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                            </svg>
                                <span>Empleados</span>
                            </a>
                        </li>
                        @endrole
                        <li>
                            <a href="{{ route('personas.index') }}" class="nav-link px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                            </svg>
                                <span>Personas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('incidencias.index') }}" class="nav-link px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                                <span>Incidencias</span>
                            </a>
                        </li>
                        @role('admin')
                        <li>
                            <a href="{{ route('peticiones.index') }}" class="nav-link px-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                            </svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-bar-chart-line" viewBox="0 0 16 16">
                    <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1zm1 12h2V2h-2zm-3 0V7H7v7zm-5 0v-3H2v3z"/>
                </svg>
                    <span>Estadísticas</span>
                </a>
            </li>
            @endrole
        </ul>
        <hr class="text-secondary">
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar d-flex align-items-center justify-content-between">
            <button class="btn btn-light burger-btn" id="menuToggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
            </svg>
            </button>
            <div>
                <button class="btn btn-light me-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                </svg>
                </button>
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                </svg>
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
                <form id="generar-pdf-form" action="{{ route('incidencias.generarPDF') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" id="pdf-fecha-inicio" name="fecha_inicio">
                    <input type="hidden" id="pdf-fecha-fin" name="fecha_fin">
                    <input type="hidden" id="pdf-estado" name="estado">
                    <button type="submit" class="btn btn-primary">Generar PDF</button>
                </form>
                @endrole
            </div>
        </div>
       
        <!-- Filters -->
        <div class="d-flex filters-container gap-2">
            <form id="busqueda-codigo-form" class="input-group input-group-sm">
                <button class="input-group-text btn btn-primary" id="basic-addon1" type="button">
                    <i class="bi bi-search"></i>
                </button>
                <input type="text" id="codigo-busqueda" class="form-control form-control-sm" placeholder="Ingrese un código">
            </form>
            <form id="filtros-form">
                @csrf
                <label for="fecha_inicio" class="form-label">Selecciona el período:</label>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex">
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control mr-2 mb-3" />
                        <span class="m-2">hasta</span>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control ml-2 mb-3" />
                    </div>
                    <select class="form-select form-select-sm w-50 m-2" aria-label="Select status" name="estado" id="estado">
                        <option value="Todos" selected>Todos</option>
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
                                @if($incidencia->usuario)
                                    @if($incidencia->usuario->empleadoAutorizado)
                                        {{ $incidencia->usuario->empleadoAutorizado->nombre }} {{ $incidencia->usuario->empleadoAutorizado->apellido }}
                                        <strong>V-</strong>{{ $incidencia->usuario->empleadoAutorizado->cedula }}
                                    @else
                                        <em>Empleado autorizado no asignado</em>
                                    @endif
                                @else
                                    <em>Usuario no asignado</em>
                                @endif
                            </td>
                            <td>
                                @if($incidencia->lider && $incidencia->lider->personas)
                                    {{ $incidencia->lider->personas->nombre ?? 'Nombre no disponible' }} 
                                    {{ $incidencia->lider->personas->apellido ?? 'Apellido no disponible' }} 
                                    <strong>V-</strong>{{ $incidencia->lider->personas->cedula ?? 'Cédula no disponible' }}
                                @else
                                    <em>No tiene un líder asignado</em>
                                @endif
                            </td>
                            <td>
                                <!-- Botón para descargar el PDF individual -->
                                <a href="{{ route('incidencias.descargar', ['slug' => $incidencia->slug]) }}" class="btn btn-primary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        class FiltroIncidencias {
            constructor(codigoInputId, fechaInicioId, fechaFinId, estadoId, tbodyId, url) {
                this.codigoInput = document.getElementById(codigoInputId);
                this.fechaInicio = document.getElementById(fechaInicioId);
                this.fechaFin = document.getElementById(fechaFinId);
                this.estado = document.getElementById(estadoId);
                this.tbody = document.getElementById(tbodyId);
                this.url = url;

                // Event listeners
                this.codigoInput.addEventListener('input', () => this.buscarPorCodigo());
                this.fechaInicio.addEventListener('change', () => this.filtrarIncidencias());
                this.fechaFin.addEventListener('change', () => this.filtrarIncidencias());
                this.estado.addEventListener('change', () => this.filtrarIncidencias());
            }

            async buscarPorCodigo() {
                const codigo = this.codigoInput.value;

                // Activar búsqueda incluso con un solo carácter
                if (codigo.length === 0) {
                    this.mostrarResultados([]);
                    return;
                }

                try {
                    const response = await fetch(this.url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ codigo })
                    });

                    const data = await response.json();
                    this.mostrarResultados(data.incidencias);
                } catch (error) {
                    console.error('Error al buscar por código:', error);
                }
            }

            async filtrarIncidencias() {
                const fechaInicio = this.fechaInicio.value;
                const fechaFin = this.fechaFin.value;
                const estado = this.estado.value;

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
                            <td>
                                <a href="/incidencias/descargar/${incidencia.slug}" class="btn btn-primary">
                                    <i class="bi bi-download"></i>
                                </a>
                            </td>
                        `;

                        this.tbody.appendChild(tr);
                    });
                } else {
                    this.tbody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron incidencias para los filtros seleccionados.</td></tr>';
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
                'codigo-busqueda',
                'fecha_inicio',
                'fecha_fin',
                'estado',
                'incidencias-tbody',
                '/filtrar-incidencia'
            );
        });

        document.getElementById('generar-pdf-form').addEventListener('submit', function (e) {
            // Obtener valores de los filtros
            const fechaInicio = document.getElementById('fecha_inicio').value || '';
            const fechaFin = document.getElementById('fecha_fin').value || '';
            const estado = document.getElementById('estado').value || 'Todos';

            // Asignar valores a los campos ocultos del formulario
            document.getElementById('pdf-fecha-inicio').value = fechaInicio;
            document.getElementById('pdf-fecha-fin').value = fechaFin;
            document.getElementById('pdf-estado').value = estado;
        });
    </script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.2/dist/chart.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>
