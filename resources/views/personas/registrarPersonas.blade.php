<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Captura de Datos</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
    <style>
        .form-control, .form-select {
            font-size: 0.9rem;
            padding: 0.5rem;
        }

        .btn {
            font-size: 0.9rem;
            padding: 0.6rem;
        }

        .alert {
            font-size: 0.9rem;
        }

        .container {
            max-width: 600px;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="bg-light">
   
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column p-3" id="sidebar">
        <a href="{{route('home')}}" class="d-flex align-items-center mb-3 text-decoration-none text-white">
            <!-- Imagen -->
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
                <a href="#layouts" class="nav-link" data-bs-toggle="collapse" aria-expanded="false">
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

    <div class="container my-5 p-4 bg-white rounded shadow-sm">
        <h1 class="mb-4 text-center">Formulario de Captura de Datos</h1>

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
            
        </div>

        <form action="{{ route('personas.store') }}" method="POST" id="form">
            @csrf
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría:</label>
                <select name="categoria" id="categoria" class="form-select" required>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id_categoriaPersona }}" {{ old('categoria') == $categoria->id_categoriaPersona ? 'selected' : '' }}>
                            {{ $categoria->nombre_categoria }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" id="apellido" name="apellido" class="form-control" value="{{ old('apellido') }}" required>
            </div>

            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula:</label>
                <input type="text" id="cedula" name="cedula" class="form-control" value="{{ old('cedula') }}" required>
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" class="form-control" value="{{ old('correo') }}" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" class="form-control" pattern="[0-9]{10>=11}" placeholder="Ej: 1234567890" value="{{ old('telefono') }}" required>
            </div>
            <div class="mb-3">
                <label for="genero" class="form-label">Género:</label>
                <select name="genero" id="genero" class="form-select" required>
                    <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
        
            <div class="mb-3">
                <label for="altura" class="form-label">Altura (cm):</label>
                <input type="number" id="altura" name="altura" class="form-control" value="{{ old('altura') }}" required min="0" step="0.01">
            </div>
        
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}" required>
            </div>
            <livewire:dropdown-persona/>

            <div class="mb-3">
                <label for="calle" class="form-label">Calle:</label>
                <input type="text" id="calle" name="calle" class="form-control" value="{{ old('calle') }}" required>
            </div>

            <div class="mb-3">
                <label for="manzana" class="form-label">Manzana:</label>
                <input type="text" id="manzana" name="manzana" class="form-control" value="{{ old('manzana') }}" required>
            </div>

            <div class="mb-3">
                <label for="bloque" class="form-label">Bloque: <small>(Solo si vive en apartamento)</small></label>
                <input type="text" id="bloque" name="bloque" class="form-control" value="{{ old('bloque') }}">
            </div>

            <div class="mb-3">
                <label for="num_vivienda" class="form-label">Número de Vivienda:</label>
                <input type="number" id="num_vivienda" name="num_vivienda" class="form-control" value="{{ old('num_vivienda') }}" required min="1" step="1">
            </div>

            <div class="mb-3">
                <label for="es_principal" class="form-label">¿Es la dirección principal?</label>
                <select name="es_principal" id="es_principal" class="form-select" required>
                    <option value="1" {{ old('es_principal') == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ old('es_principal') == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Enviar</button>
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
    <!-- Ensure this is included for dropdown and collapse functionality -->
</body>

</html>
