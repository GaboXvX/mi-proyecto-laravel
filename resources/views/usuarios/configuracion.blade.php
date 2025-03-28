<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Cuenta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <style>
        .form-control-sm {
            font-size: 12px;
            padding: 6px 12px;
            height: 34px;
            width: 100%;
        }

        .btn-sm {
            font-size: 12px;
            padding: 6px 15px;
        }
    </style>
</head>

<body>
   <nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <a href="{{route('home')}}" class="d-flex align-items-center mb-3 text-decoration-none text-white">
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
  <div class="form-container">
    <div class="container py-4">
        <div >
            <div class="card-body">
                <h2 class="text-center text-primary mb-3">Configuración de Cuenta</h2>

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
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('usuarios.cambiar', $usuario->id_usuario) }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control form-control-sm" id="inputNombre"
                                    name="nombre" value="{{ old('nombre', $usuario->nombre) }}"
                                    placeholder="Ingrese su nombre" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputApellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control form-control-sm" id="inputApellido"
                                    name="apellido" value="{{ old('apellido', $usuario->apellido) }}"
                                    placeholder="Ingrese su apellido" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputCedula" class="form-label">Cédula</label>
                                <input type="text" class="form-control form-control-sm" id="inputCedula"
                                    name="cedula" value="{{ $usuario->cedula }}" placeholder="Ingrese su cédula"
                                    readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputCorreo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control form-control-sm" id="inputCorreo"
                                    name="email" value="{{ old('email', $usuario->email) }}"
                                    placeholder="Ingrese su correo electrónico" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputUsuario" class="form-label">Nombre de Usuario</label>
                                <input type="text" class="form-control form-control-sm" id="inputUsuario"
                                    name="nombre_usuario"
                                    value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}"
                                    placeholder="Ingrese su nombre de usuario" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputContraseña" class="form-label">Contraseña</label>
                                <input type="password" class="form-control form-control-sm" id="inputContraseña"
                                    name="contraseña" placeholder="Ingrese su nueva contraseña">
                            </div>
                        </div>
                    </div>

                    <!-- Campos adicionales -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputGenero" class="form-label">Género</label>
                                <select class="form-control form-control-sm" id="inputGenero" name="genero" required>
                                    <option value="M" {{ old('genero', $usuario->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('genero', $usuario->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputFechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control form-control-sm" id="inputFechaNacimiento"
                                    name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $usuario->fecha_nacimiento) }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputAltura" class="form-label">Altura (cm)</label>
                                <input type="number" class="form-control form-control-sm" id="inputAltura"
                                    name="altura" value="{{ old('altura', $usuario->altura) }}" min="0" step="0.01"
                                    placeholder="Ingrese su altura" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" class="btn btn-success btn-sm px-4">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>
</body>

</html>