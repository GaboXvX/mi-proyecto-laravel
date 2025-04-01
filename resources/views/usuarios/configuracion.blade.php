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
                
                <!-- Mostrar datos del usuario -->
                <table class="profile-table table table-bordered">
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ auth()->user()->empleadoAutorizado->nombre }}</td>
                    </tr>
                    <tr>
                        <th>Apellido:</th>
                        <td>{{ auth()->user()->empleadoAutorizado->apellido }}</td>
                    </tr>
                    <tr>
                        <th>Cédula:</th>
                        <td>{{ auth()->user()->empleadoAutorizado->cedula }}</td>
                    </tr>
                    <tr>
                        <th>Género:</th>
                        <td>{{ auth()->user()->empleadoAutorizado->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                    </tr>
                    <tr>
                        <th>Fecha de Nacimiento:</th>
                        <td>{{ auth()->user()->empleadoAutorizado->fecha_nacimiento }}</td>
                    </tr>
                    <tr>
                        <th>Altura:</th>
                        <td>{{ auth()->user()->empleadoAutorizado->altura }} cm</td>
                    </tr>
                    <tr>
                        <th>Teléfono:</th>
                        <td>{{ auth()->user()->empleadoAutorizado->telefono }}</td>
                    </tr>
                    <tr>
                        <th>Cargo:</th>
                        <td>{{ auth()->user()->empleadoAutorizado->cargo->nombre }}</td>
                    </tr>
                </table>

                <hr>

                <form action="{{ route('usuarios.cambiar', $usuario->id_usuario) }}" method="POST" id="configuracionForm">
                    @csrf

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inputCorreo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control form-control-sm" id="inputCorreo"
                                name="email" value="{{ old('email', $usuario->email) }}"
                                placeholder="Ingrese su correo electrónico" required>
                            <small id="correoFeedback" class="text-muted"></small>
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
                                <small id="usuarioFeedback" class="text-muted"></small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="inputContraseña" class="form-label">Contraseña</label>
                            <input type="password" class="form-control form-control-sm" id="inputContraseña"
                                name="contraseña" placeholder="Ingrese su nueva contraseña">
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        <button type="submit" class="btn btn-success btn-sm px-4" id="submitButton" disabled>Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/script.js') }}"></script>

<script>
    const correoInput = document.getElementById('inputCorreo');
    const usuarioInput = document.getElementById('inputUsuario');
    const submitButton = document.getElementById('submitButton');
    let correoDisponible = true;
    let usuarioDisponible = true;

    function validarFormulario() {
        submitButton.disabled = !(correoDisponible && usuarioDisponible);
    }

    correoInput.addEventListener('input', function () {
        const correo = this.value;
        const feedback = document.getElementById('correoFeedback');

        if (correo.trim() === '') {
            feedback.textContent = '';
            correoDisponible = true;
            validarFormulario();
            return;
        }

        fetch(`/validar-correo/${correo}?excluir={{ $usuario->id_usuario }}`)
            .then(response => response.json())
            .then(data => {
                if (data.disponible) {
                    feedback.textContent = '¡Correo disponible!';
                    feedback.classList.remove('text-danger');
                    feedback.classList.add('text-success');
                    correoDisponible = true;
                } else {
                    feedback.textContent = 'El correo ya está en uso.';
                    feedback.classList.remove('text-success');
                    feedback.classList.add('text-danger');
                    correoDisponible = false;
                }
                validarFormulario();
            })
            .catch(() => {
                feedback.textContent = 'Error al validar el correo.';
                feedback.classList.remove('text-success');
                feedback.classList.add('text-danger');
                correoDisponible = false;
                validarFormulario();
            });
    });

    usuarioInput.addEventListener('input', function () {
        const nombreUsuario = this.value;
        const feedback = document.getElementById('usuarioFeedback');

        if (nombreUsuario.trim() === '') {
            feedback.textContent = '';
            usuarioDisponible = true;
            validarFormulario();
            return;
        }

        fetch(`/validar-usuario/${nombreUsuario}?excluir={{ $usuario->id_usuario }}`)
            .then(response => response.json())
            .then(data => {
                if (data.disponible) {
                    feedback.textContent = '¡Nombre de usuario disponible!';
                    feedback.classList.remove('text-danger');
                    feedback.classList.add('text-success');
                    usuarioDisponible = true;
                } else {
                    feedback.textContent = 'El nombre de usuario ya está en uso.';
                    feedback.classList.remove('text-success');
                    feedback.classList.add('text-danger');
                    usuarioDisponible = false;
                }
                validarFormulario();
            })
            .catch(() => {
                feedback.textContent = 'Error al validar el nombre de usuario.';
                feedback.classList.remove('text-success');
                feedback.classList.add('text-danger');
                usuarioDisponible = false;
                validarFormulario();
            });
    });
</script>
</body>

</html>