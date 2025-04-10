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

    <main class="main-content">
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
                        <li><a class="dropdown-item" href="{{ route('usuarios.configuracion') }}">Perfil</a></li>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registroPersonaModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-plus" viewBox="0 0 16 16">
                            <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                            <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                        </svg>
                    </button>
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
                                                <a href="{{ route('personas.incidencias', $persona->slug) }}" class="btn btn-warning btn-sm">Ver Incidencias</a>
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

    <!-- Modal -->
    <div class="modal fade" id="registroPersonaModal" tabindex="-1" aria-labelledby="registroPersonaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="registroPersonaForm" action="{{ route('personas.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="registroPersonaModalLabel">Registrar Persona</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="global-alerts" class="alert d-none"></div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="categoria" class="form-label">Categoría:</label>
                                <select name="categoria" id="categoria" class="form-select" required>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id_categoriaPersona }}">{{ $categoria->nombre_categoria }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="genero" class="form-label">Género:</label>
                                <select name="genero" id="genero" class="form-select" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="11">
                            </div>
                            <div class="col-md-6">
                                <label for="apellido" class="form-label">Apellido:</label>
                                <input type="text" id="apellido" name="apellido" class="form-control" required maxlength="11">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cedula" class="form-label">Cédula:</label>
                                <input type="text" id="cedula" name="cedula" class="form-control" required maxlength="8">
                                <div class="invalid-feedback"></div>

                            </div>
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo Electrónico:</label>
                                <input type="email" id="correo" name="correo" class="form-control" required>
                                <div class="invalid-feedback"></div>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="tel" id="telefono" name="telefono" class="form-control" pattern="[0-9]{10,11}" placeholder="Ej: 1234567890" required>
                            </div>
                            <div class="col-md-6">
                                <label for="altura" class="form-label">Altura (cm):</label>
                                <input type="number" id="altura" name="altura" class="form-control" required step="0.01" min="0.1" placeholder="Ej: 1.75">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
                        </div>

                        <livewire:dropdown-persona/>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="calle" class="form-label">Calle:</label>
                                <input type="text" id="calle" name="calle" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="manzana" class="form-label">Manzana:</label>
                                <input type="text" id="manzana" name="manzana" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bloque" class="form-label">Bloque: <small>(Solo si vive en apartamento)</small></label>
                                <input type="text" id="bloque" name="bloque" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="num_vivienda" class="form-label">Número de Vivienda:</label>
                                <input type="number" id="num_vivienda" name="num_vivienda" class="form-control" required min="1" step="1">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="es_principal" class="form-label">¿Es la dirección principal?</label>
                            <select name="es_principal" id="es_principal" class="form-select" required>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitRegistroPersona">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración del buscador de personas
            const originalData = @json($personas->items());
            new BuscadorPersonas('buscar', 'personas-tbody', '{{ route('personas.buscar') }}', originalData);

            // Mostrar mensaje de éxito si está presente en la sesión
            const successMessage = "{{ session('success') }}";
            if (successMessage) {
                const alertContainer = document.createElement('div');
                alertContainer.className = 'alert alert-success';
                alertContainer.textContent = successMessage;
                document.querySelector('.table-container').prepend(alertContainer);
                setTimeout(() => alertContainer.remove(), 5000);
            }

            // Validación en tiempo real para cédula
            document.getElementById('cedula').addEventListener('blur', async function() {
                const cedula = this.value.trim();
                const errorElement = this.nextElementSibling;
                
                if (cedula.length < 6) return;
                
                try {
                    const response = await fetch('{{ route('personas.validar-cedula') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ cedula })
                    });
                    
                    const data = await response.json();
                    
                    if (data.exists) {
                        this.classList.add('is-invalid');
                        errorElement.textContent = 'Esta cédula ya está registrada';
                    } else {
                        this.classList.remove('is-invalid');
                        errorElement.textContent = '';
                    }
                } catch (error) {
                    console.error('Error al validar cédula:', error);
                }
            });

            // Validación en tiempo real para correo
            document.getElementById('correo').addEventListener('blur', async function() {
                const correo = this.value.trim();
                const errorElement = this.nextElementSibling;
                
                if (correo.length === 0 || !correo.includes('@')) return;
                
                try {
                    const response = await fetch('{{ route('personas.validar-correo') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ correo })
                    });
                    
                    const data = await response.json();
                    
                    if (data.exists) {
                        this.classList.add('is-invalid');
                        errorElement.textContent = 'Este correo ya está registrado';
                    } else {
                        this.classList.remove('is-invalid');
                        errorElement.textContent = '';
                    }
                } catch (error) {
                    console.error('Error al validar correo:', error);
                }
            });

            // Manejo del submit del formulario
            document.getElementById('registroPersonaForm').addEventListener('submit', async function(event) {
                event.preventDefault();
                
                const form = event.target;
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const globalAlerts = document.getElementById('global-alerts');
                
                // Limpiar errores previos
                globalAlerts.classList.add('d-none');
                globalAlerts.innerHTML = '';
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                    const feedback = el.nextElementSibling;
                    if (feedback) feedback.textContent = '';
                });

                // Mostrar estado de carga
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Procesando...
                `;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        // Mostrar errores de campo específico
                        if (data.errors) {
                            Object.entries(data.errors).forEach(([field, messages]) => {
                                const input = form.querySelector(`[name="${field}"]`);
                                const errorElement = input?.nextElementSibling;
                                
                                if (input && errorElement) {
                                    input.classList.add('is-invalid');
                                    errorElement.textContent = messages[0];
                                }
                            });
                        }
                        
                        // Mostrar mensaje global si existe
                        if (data.message) {
                            globalAlerts.classList.remove('d-none');
                            globalAlerts.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                            globalAlerts.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else {
                        // Éxito - cerrar modal y recargar
                        const modal = bootstrap.Modal.getInstance(document.getElementById('registroPersonaModal'));
                        modal.hide();
                        
                        // Mostrar mensaje de éxito
                        const successAlert = document.createElement('div');
                        successAlert.className = 'alert alert-success';
                        successAlert.textContent = data.message || 'Registro exitoso';
                        document.querySelector('.table-container').prepend(successAlert);
                        setTimeout(() => successAlert.remove(), 5000);
                        
                        // Recargar la página
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    globalAlerts.classList.remove('d-none');
                    globalAlerts.innerHTML = `
                        <div class="alert alert-danger">
                            Error de conexión. Por favor intente nuevamente.
                        </div>
                    `;
                    globalAlerts.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Registrar';
                }
            });
        });

        class BuscadorPersonas {
            constructor(inputId, tbodyId, url, originalData) {
                this.input = document.getElementById(inputId);
                this.tbody = document.getElementById(tbodyId);
                this.url = url;
                this.originalData = originalData;
                this.input.addEventListener('input', () => this.buscarPersonas());
            }

            async buscarPersonas() {
                const query = this.input.value.trim();
                if (!query) {
                    this.mostrarResultados(this.originalData);
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
                                <a href="/persona/${persona.slug}/incidencias" class="btn btn-warning btn-sm">Ver Incidencias</a>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }
        }
    </script>
</body>

</html>
