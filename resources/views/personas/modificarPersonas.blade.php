<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Persona</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
    <style>
        .container {
            max-width: 500px;
        }

        .form-control,
        .form-select {
            font-size: 0.85rem;
            padding: 0.4rem;
        }

        .btn {
            font-size: 0.85rem;
            padding: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 0.75rem;
        }

        h1 {
            font-size: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .alert {
            font-size: 0.85rem;
        }

        .btn-add-address {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 0.5rem 1rem; /* Reduce padding */
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
            font-size: 0.75rem; /* Reduce font size */
        }

        .btn-add-address:hover {
            background-color: #0b5ed7;
        }

        .btn-add-address i {
            font-size: 1rem; /* Reduce icon size */
        }
    </style>
</head>

<body class="bg-light">
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
                        <li>
                            <a href="{{ route('movimientos.index') }}" class="nav-link px-3">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Movimientos</span>
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
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="container my-5 p-4 bg-white rounded shadow-sm">
            <h1 class="mb-4 text-center">Editar persona</h1>

            @if (session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('personas.index') }}" class="btn btn-secondary btn-sm">Volver</a>
                <a href="{{ route('personas.agregarDireccion', $persona->slug) }}" class="btn btn-secondary btn-sm">Añadir dirección</a>
                <a href="{{ route('personas.modificarDireccion', $persona->slug) }}" class="btn btn-secondary btn-sm">Modificar dirección</a>
            </div>
            <br>
            
            <form action="{{ route('personas.update', $persona->slug) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="id_categoriaPersona" class="form-label">Categoría de Persona:</label>
                    <select id="id_categoriaPersona" name="categoria" class="form-select" required>
                        <option value="" disabled selected>--Seleccione--</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id_categoriaPersona }}" {{ old('id_categoriaPersona', $persona->id_categoriaPersona) == $categoria->id_categoriaPersona ? 'selected' : '' }}>
                                {{ $categoria->nombre_categoria }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control"
                        value="{{ old('nombre', $persona->nombre) }}" required>
                </div>

                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" class="form-control"
                        value="{{ old('apellido', $persona->apellido) }}" required>
                </div>

                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula:</label>
                    <input type="number" id="cedula" name="cedula" class="form-control"
                        value="{{ old('cedula', $persona->cedula) }}" required>
                </div>

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" class="form-control"
                        value="{{ old('correo', $persona->correo) }}" required>
                </div>

                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" class="form-control"
                        value="{{ old('telefono', $persona->telefono) }}" required>
                </div>

              
                <button type="submit" class="btn btn-primary w-100">Actualizar</button>
            </form>
            
            
        </div>

        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/script.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    document.querySelectorAll('.alert').forEach(function(alert) {
                        alert.style.display = 'none';
                    });
                }, 2000);
            });
        </script>
</body>

</html>